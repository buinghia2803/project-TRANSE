<?php

namespace app\Console\Commands\Seed;

use App\Models\MCode;
use App\Models\MDistinction;
use App\Models\MProduct;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;

class ProductV3Command extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'transe:seed:product_v3 {source=resources/assets/excels/m_product_group_v3.xlsx}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed data for product group. m_products, m_distinctions, m_code, m_product_code version 3';

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
     * @return int
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
            'B' => 2,
            'C' => 3, // m_products.products_number
            'D' => 4, // m_distinction.name
            'E' => 5, // m_code.name
            'F' => 6, // m_products.is_parent
            'G' => 7, // m_products.parent_id
            'H' => 8, // m_products.name
        ];

        $startRow = '4';
        $endRow = $worksheet->getHighestRow();

        $productData = [];
        for ($i = $startRow; $i <= $endRow; $i++) {
            $branchNumberCode = $worksheet->getCellByColumnAndRow($columns['B'], $i)->getValue();
            $distinctionName = $worksheet->getCellByColumnAndRow($columns['D'], $i)->getValue();
            $codeName = $worksheet->getCellByColumnAndRow($columns['E'], $i)->getValue();
            $productNumber = $worksheet->getCellByColumnAndRow($columns['C'], $i)->getValue();
            $isParent = $worksheet->getCellByColumnAndRow($columns['F'], $i)->getValue();
            $parentIdProductNumber = $worksheet->getCellByColumnAndRow($columns['G'], $i)->getValue();
            $productName = $worksheet->getCellByColumnAndRow($columns['H'], $i)->getValue();

            if (!empty($productName)) {
                // Product Data
                $productData[$productNumber]['admin_id'] = 1;
                $productData[$productNumber]['type'] = 1;
                $productData[$productNumber]['total_order'] = 0;
                $productData[$productNumber]['name'] = $productName;
                $productData[$productNumber]['products_number'] = $productNumber;
                $productData[$productNumber]['parent_id_product_number'] = $parentIdProductNumber;
                $productData[$productNumber]['is_parent'] = $isParent ? MProduct::IS_PARENT : MProduct::IS_NOT_PARENT;
                // Distinction Data
                $productData[$productNumber]['distinction_name'] = $distinctionName;

                // Code Data
                $productData[$productNumber]['code'][] = [
                    'name' => $codeName,
                    'branch_number' => $branchNumberCode,
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
                        'branch_number' => $code['branch_number'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                } else {
                    $codeRecord = $checkExistName;
                }
                $codeInsertData[] = $codeRecord;
            }

            $product['m_distinction_id'] = $distinction->id;

            //set parent_id
            if (!empty($product['parent_id_product_number'])) {
                $parentModel = MProduct::where('products_number', $product['parent_id_product_number'])->where('is_parent', MProduct::IS_PARENT)->first();
                if ($parentModel) {
                    $product['parent_id'] = $parentModel->id;
                }
            }
            unset($product['parent_id_product_number']);
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
