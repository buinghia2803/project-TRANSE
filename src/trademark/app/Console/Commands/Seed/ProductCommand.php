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

class ProductCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'transe:seed:product {source=resources/assets/excels/m_product_group.xlsx}';

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

        // Get data to prepare insert
        $distinctionData = [];
        $codeData = [];
        $productData = [];
        for ($i = $startRow; $i <= $endRow; $i++) {
            $distinctionName = $worksheet->getCellByColumnAndRow($columns['D'], $i)->getValue();
            $codeName = $worksheet->getCellByColumnAndRow($columns['E'], $i)->getValue();
            $productNumber = $worksheet->getCellByColumnAndRow($columns['C'], $i)->getValue();
            $productBlock = $worksheet->getCellByColumnAndRow($columns['F'], $i)->getValue();
            $productName = $worksheet->getCellByColumnAndRow($columns['G'], $i)->getValue();

            if (!empty($productName)) {
                $distinctionData[] = $distinctionName;
                $codeData[] = $codeName;
                $productData[] = [
                    'admin_id' => 1,
                    'products_number' => $productNumber,
                    'name' => $productName,
                    'type' => 1,
                    'total_order' => 0,
                    'block' => $productBlock,
                    'code_name' => $codeName,
                    'distinction_name' => $distinctionName,
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

        // Create distinction
        $distinctionData = array_unique($distinctionData);
        $distinctionInsertData = [];
        foreach ($distinctionData as $key => $item) {
            $distinctionInsertData[] = [
                'admin_id' => 1,
                'name' => $item,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        MDistinction::insert($distinctionInsertData);
        $distinctions = MDistinction::all();

        // Create code
        $codeData = array_unique($codeData);
        $codeInsertData = [];
        foreach ($codeData as $key => $item) {
            $codeInsertData[] = [
                'admin_id' => 1,
                'name' => $item,
                'type' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        MCode::insert($codeInsertData);
        $codes = MCode::all();

        // Create Product and Product Code
        foreach ($productData as $item) {
            $distinction = $distinctions->where('name', $item['distinction_name'])->first();
            $code = $codes->where('name', $item['code_name'])->first();

            $item['m_distinction_id'] = $distinction->id;
            $product = MProduct::create($item);

            $product->mProductCode()->create([
                'm_code_id' => $code->id,
            ]);
        }

        $this->info('Create product group success.');
    }
}
