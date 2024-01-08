<?php

namespace Database\Seeders;

use App\Models\Agent;
use App\Models\AgentGroup;
use App\Models\MDistinction;
use App\Models\MNation;
use App\Models\MPrefecture;
use App\Models\MProduct;
use App\Models\User;
use App\Models\Admin;
use App\Models\Payment;
use App\Models\PayerInfo;
use App\Models\Trademark;
use App\Models\AppTrademark;
use App\Models\TrademarkInfo;
use App\Models\AppTrademarkProd;
use Faker\Generator as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Carbon\Carbon;

class DataDummySeeder extends Seeder
{
    /**
     * The current Faker instance.
     *
     * @var \Faker\Generator
     */
    protected $faker;

    protected $imgUrlSample = [];
    protected $adminId = [];

    /**
     * Create a new seeder instance.
     *
     * @return void
     */
    public function __construct(Faker $faker)
    {
        $this->adminId = Admin::all()->pluck('id')->toArray();
        $this->productIds = MProduct::all()->pluck('id')->toArray();
        $this->distinctionIds = MDistinction::all()->pluck('id')->toArray();
        $this->prefectureIds = MPrefecture::all()->pluck('id')->toArray();
        $this->nationIds = MNation::all()->pluck('id')->toArray();
        $this->agentIds = Agent::all()->pluck('id')->toArray();
        $this->agentGroupIds = AgentGroup::all()->pluck('id')->toArray();
        $this->faker = $faker;
        for ($i=1000; $i < 1090; $i++) {
            $this->imgUrlSample[] = 'https://picsum.photos/id/'.$i.'/200/300';
        }
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Truncate table
        Schema::disableForeignKeyConstraints();
        DB::table('users')->truncate();
        DB::table('payer_infos')->truncate();
        DB::table('trademarks')->truncate();
        DB::table('app_trademarks')->truncate();
        DB::table('app_trademark_prods')->truncate();
        DB::table('trademark_infos')->truncate();
        DB::table('payments')->truncate();
        Schema::enableForeignKeyConstraints();

        $this->command->info('------------ START DUMMY DATA ------------');
        $this->command->getOutput()->progressStart(31);
        for ($i = 1; $i < 31; $i++) {
            try {
                DB::beginTransaction();

                $this->createUsers($i);
                $this->createPayerInfo($i);
                $this->createTrademark($i);
                $this->createAppTrademark($i);
                $this->createAppTrademarkProd($i);
                $this->createTrademarkInfo($i);
                $this->createPayment($i);

                DB::commit();

                $this->command->getOutput()->progressAdvance();
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error($e);
                break;
            }
        }
        $this->command->getOutput()->progressFinish();
    }

    public function createAppTrademark($i)
    {
        $trademarkIds = Trademark::select('id')->get()->pluck('id')->toArray();

        return AppTrademark::create([
            "trademark_id" => $trademarkIds[rand(0,count($trademarkIds) - 1)],
            "admin_id" => $this->adminId[rand(0,count($this->adminId) - 1)],
            "agent_group_id" => $this->agentGroupIds[rand(0,count($this->agentGroupIds) - 1)],
            "cancellation_deadline" => Carbon::now()->subDays($this->faker->numberBetween(1, 30))->format('Y-m-d'),
            "comment_office" => $this->faker->text(),
            "status" => rand(1,3),
            "pack" => rand(1,3),
            "is_mailing_regis_cert" => rand(0,1),
            "period_registration" => rand(0,1),
            "is_cancel" => rand(0,1),
        ]);
    }

    public function createAppTrademarkProd($i)
    {
        $appTrademarkIds = AppTrademark::select('id')->get()->pluck('id')->toArray();

        return AppTrademarkProd::create([
            'app_trademark_id' => $appTrademarkIds[rand(0,count($appTrademarkIds) - 1)],
            'm_product_id' => $this->productIds[rand(0,count($this->productIds) - 1)],
            'is_apply' => rand(0,1),
            'is_remove' => rand(0,1),
            'is_new_prod' => rand(0,1),
            'is_block' => rand(0,1),
        ]);
    }

    public function createPayerInfo($i)
    {
        return PayerInfo::create([
            'target_id' => $i,
            'payment_type' => $this->faker->numberBetween(1,2),
            'payer_type' => $this->faker->numberBetween(1,2),
            'm_nation_id' => $this->nationIds[rand(0,count($this->nationIds) - 1)],
            'payer_name' => $this->faker->name,
            'payer_name_furigana' => $this->faker->name,
            'postal_code' => $this->faker->postcode(),
            'm_prefecture_id' => $this->prefectureIds[rand(0,count($this->prefectureIds) - 1)],
            'address_second' => $this->faker->address(),
            'address_three' => $this->faker->address(),
            'type' => rand(1,9),
        ]);
    }

    public function createUsers($i)
    {
        return User::create([
            'name_trademark' => $this->faker->name,
            'is_image_trademark' => $this->faker->numberBetween(0,1),
            'email' => $this->faker->email,
            'user_number' => 'LA'. Str::random(3),
            'info_type_acc' => rand(1,2),
            'info_name' => $this->faker->name,
            'info_name_furigana' => $this->faker->name,
            'info_corporation_number' => Str::random(40),
            'info_nation_id' => $this->nationIds[rand(0,count($this->nationIds) - 1)],
            'info_postal_code' => $this->faker->postcode(),
            'info_prefectures_id' => $this->prefectureIds[rand(0,count($this->prefectureIds) - 1)],
            'info_address_second' => $this->faker->address(),
            'info_address_three' => $this->faker->address(),
            'info_phone' => $this->faker->phoneNumber(),
            'info_member_id' => rand(10000000, 99999999),
            'password' => bcrypt('Test1234'),
            'info_gender' => $this->faker->numberBetween(1,2),
            'info_birthday' => Carbon::now()->subDays($this->faker->numberBetween(1, 30))->format('Y-m-d'),
            'info_question' =>  $this->faker->sentence(10),
            'info_answer' => $this->faker->sentence(10),
            'contact_type_acc' => $this->faker->numberBetween(1,2),
            'contact_name' => $this->faker->name,
            'contact_name_furigana' =>  $this->faker->name,
            'contact_name_department' =>  $this->faker->name,
            'contact_name_department_furigana' =>  $this->faker->name,
            'contact_name_manager' =>  $this->faker->name,
            'contact_name_manager_furigana' =>  $this->faker->name,
            'contact_nation_id' => $this->nationIds[rand(0,count($this->nationIds) - 1)],
            'contact_postal_code' => $this->faker->postcode(),
            'contact_prefectures_id' => $this->prefectureIds[rand(0,count($this->prefectureIds) - 1)],
            'contact_address_second' => $this->faker->address(),
            'contact_address_three' => $this->faker->address(),
            'contact_phone' => $this->faker->phoneNumber(),
            'contact_email_second' =>  $this->faker->email,
            'contact_email_three' =>  $this->faker->email,
            'status' => User::ENABLED,
            'status_withdraw' => User::STATUS_WITHDRAW_INACTIVE,
            'reason_withdraw' => $this->faker->text(),
            'problems' => rand(1,3),
        ]);
    }

    public function createTrademark($i)
    {
        $userIds = User::select('id')->get()->pluck('id')->toArray();

        $arrTrademark = ['LA', 'Q' ];
        return Trademark::create([
            "user_id" => $userIds[rand(0,count($userIds) - 1)],
            "trademark_number" => $arrTrademark[rand(0,1)] . Str::random(9),
            "application_number" => Str::random(11),
            "type_trademark" => $this->faker->numberBetween(1,2),
            "name_trademark" => $this->faker->name,
            "image_trademark" => $this->imgUrlSample[rand(1,70)],
            "reference_number" => ["引例番号1", "引例番号2"][rand(0,1)],
            "status_management" => rand(0,1),
        ]);
    }

    public function createTrademarkInfo($i)
    {
        return TrademarkInfo::create([
            'target_id' => $i,
            'type_acc' => rand(1,2),
            'name' => $this->faker->name,
            'm_nation_id' => $this->nationIds[rand(0,count($this->nationIds) - 1)],
            'm_prefecture_id' => $this->prefectureIds[rand(0,count($this->prefectureIds) - 1)],
            'address_second' => $this->faker->address(),
            'address_three' => $this->faker->address(),
            'type' => rand(1,3),
        ]);
    }

    public function createPayment($i)
    {
        $payerInfo = PayerInfo::select('id')->get()->pluck('id')->toArray();

        return Payment::create([
            'target_id' => $i,
            'payer_info_id' => $payerInfo[rand(0,count($payerInfo) - 1)],
            'quote_number' => rand(10000,30000),
            'invoice_number' => rand(10000,30000),
            'receipt_number' => rand(10000,30000),
            'cost_service_base' => rand(10000,30000),
            'cost_service_add_prod' => rand(10000,30000),
            'cost_bank_transfer' => rand(10000,30000),
            'subtotal' => rand(10000,30000),
            'commission' => rand(10000,30000),
            'tax' => rand(10000,30000),
            'print_fee' => rand(10000,30000),
            'cost_print_application_one_distintion' => rand(10000,30000),
            'cost_print_application_add_distintion' => rand(10000,30000),
            'costs_correspondence_of_one_prod' => rand(10000,30000),
            'reduce_number_distitions' => rand(10000,30000),
            'cost_change_address' => rand(10000,30000),
            'cost_change_name' => rand(10000,30000),
            'cost_print_name' => rand(10000,30000),
            'cost_print_address' => rand(10000,30000),
            'cost_print_application' => rand(10000,30000),
            'cost_print_5year_or_10year' => rand(10000,30000),
            'cost_registration_certificate' => rand(10000,30000),
            'extension_of_period_before_expiry' => rand(10000,30000),
            'application_discount' => rand(10000,30000),
            'cost_application' => rand(10000,30000),
            'cost_5_year_one_distintion' => rand(10000,30000),
            'cost_10_year_one_distintion' => rand(10000,30000),
            'total_amount' => rand(10000,30000),
            'tax_withholding' => rand(10000,30000),
            'payment_amount' => rand(10000,30000),
            'type' => 1,
            'is_confirm' => rand(0,1),
            'comment' => $this->faker->paragraph(),
            'payment_date' => Carbon::now()->subDays($this->faker->numberBetween(1, 30))->format('Y-m-d'),
            'is_treatment' => rand(0,1),
            'payment_status' => rand(0,2),
        ]);
    }
}
