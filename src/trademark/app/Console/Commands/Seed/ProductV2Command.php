<?php

namespace App\Console\Commands\Seed;

use App\Models\MCode;
use App\Models\MDistinction;
use App\Models\MProduct;
use App\Models\MProductCode;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;

class ProductV2Command extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'transe:seed:product_v2 {source=resources/assets/excels/m_product_group.xlsx}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed data for product group. m_products, m_distinctions, m_code, m_product_code';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $sourcePath = $this->arguments('source')['source'];

        // Reading excel $path
        $reader = new Xlsx();
        $reader->setLoadSheetsOnly(0);
        $spreadsheet = $reader->load(base_path($sourcePath));

        $worksheet = $spreadsheet->getActiveSheet();

        // Set column name
        $columns = [
            'C' => 3, // m_products.products_number
            'D' => 4, // m_distinction.name
            'E' => 5, // m_code.name
            'F' => 6, // m_products.block
            'G' => 7, // m_products.name
        ];

        $startRow = '4';
        $endRow = $worksheet->getHighestRow();

        $productData = [];
        for ($i = $startRow; $i <= $endRow; $i++) {
            $distinctionName = $worksheet->getCellByColumnAndRow($columns['D'], $i)->getValue();
            $codeName = $worksheet->getCellByColumnAndRow($columns['E'], $i)->getValue();
            $productNumber = $worksheet->getCellByColumnAndRow($columns['C'], $i)->getValue();
            $productBlock = $worksheet->getCellByColumnAndRow($columns['F'], $i)->getValue();
            $productName = $worksheet->getCellByColumnAndRow($columns['G'], $i)->getValue();

            if (!empty($productName)) {
                // Product Data
                $productData[$productNumber]['admin_id'] = 1;
                $productData[$productNumber]['type'] = 1;
                $productData[$productNumber]['total_order'] = 0;
                $productData[$productNumber]['name'] = $productName;
                $productData[$productNumber]['products_number'] = $productNumber;
                $productData[$productNumber]['block'] = $productBlock;

                // Distinction Data
                $productData[$productNumber]['distinction_name'] = $distinctionName;

                // Code Data
                $productData[$productNumber]['code'][] = [
                    'name' => $codeName,
                ];
            }
        }

        // Truncate table
        Schema::disableForeignKeyConstraints();
        DB::table('m_products')->truncate();
        DB::table('m_code')->truncate();
        DB::table('m_product_codes')->truncate();
        DB::table('m_distinctions')->truncate();
        Schema::enableForeignKeyConstraints();

        foreach ($productData as $product) {
            $distinctionName = $product['distinction_name'];
            $checkExistDistinction = MDistinction::where('name', $distinctionName)->first();
            if (empty($checkExistDistinction)) {
                $distinction = MDistinction::create([
                    'admin_id' => 1,
                    'name' => $distinctionName,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } else {
                $distinction = $checkExistDistinction;
            }

            $codes = $product['code'] ?? [];
            $codeInsertData = [];
            foreach ($codes as $code) {
                $codeName = $code['name'] ?? null;

                $checkExistName = MCode::where('name', $codeName)->first();
                if (empty($checkExistName)) {
                    $codeRecord = MCode::create([
                        'admin_id' => 1,
                        'name' => $code['name'] ?? null,
                        'type' => 1,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                } else {
                    $codeRecord = $checkExistName;
                }

                $codeInsertData[] = $codeRecord;
            }

            $product['m_distinction_id'] = $distinction->id;
            $productRecord = MProduct::create($product);

            foreach ($codeInsertData as $code) {
                $productRecord->mProductCode()->create([
                    'm_code_id' => $code->id,
                ]);
            }
        }

        $this->info('Create product group success.');
    }
}
