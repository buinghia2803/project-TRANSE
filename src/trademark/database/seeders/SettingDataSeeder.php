<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SettingDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Truncate table
        Schema::disableForeignKeyConstraints();
        DB::table('settings')->truncate();
        Schema::enableForeignKeyConstraints();

        $defaultSettings = [
            [
                'key' => 'tax',
                'value' => 10,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'key' => 'publisher_postal_code',
                'value' => '〒135-0063',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'key' => 'publisher_address_first',
                'value' => '東京都江東区有明3-7-26',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'key' => 'publisher_address_second',
                'value' => '有明フロンティアビルB棟9階',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'key' => 'publisher_tel',
                'value' => '3-0000-0000',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'key' => 'publisher_fax',
                'value' => '3-0000-0000',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'key' => 'publisher_registration_number',
                'value' => '123456',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'key' => 'stamp',
                'value' => 'common/images/print/company_stamp.png',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'key' => 'bank_information',
                'value' => 'ABCD銀行　何処町支店　普通口座 1234567<br/>【口座名義】エーエムエストツキヨシヨウヒヨウジムシヨ<br/><span style="margin-left:65px">ニシザキ カズノリ</span>',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'key' => 'payment_due_date',
                'value' => 7,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'key' => 'register_due_date',
                'value' => 3,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'key' => 'transfer_destination',
                'value' => 'xx銀行 xx支店 普通 123456',
                'created_at' => now(),
                'updated_at' => now()
            ],
        ];

        Setting::insert($defaultSettings);
    }
}
