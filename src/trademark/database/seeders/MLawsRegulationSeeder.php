<?php

namespace Database\Seeders;

use App\Models\MLawsRegulation;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Faker\Generator as Faker;
use Carbon\Carbon;

class MLawsRegulationSeeder extends Seeder
{
    /**
     * The current Faker instance.
     *
     * @var \Faker\Generator
     */
    protected $faker;

    /**
     * Create a new seeder instance.
     *
     * @return void
     */
    public function __construct(Faker $faker)
    {
        $this->faker = $faker;
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
        DB::table('m_laws_regulations')->truncate();
        Schema::enableForeignKeyConstraints();

        //  1: 3条柱書き（使用意思）,
        //  rank: B
        //  2: 3条柱書き（資格規制など）,
        //  rank: C
        //  3: 3条1項各号（識別力）又は・及び4条1項16号（品質誤認）,
        //  rank: D
        //  4: 4条1項1～7号（公益的理由）,
        //  rank: D
        //  5: 4条1項8～19号（私益的理由、16号を除く）,
        //  rank: D
        //  6: 8条（同日出願）,
        //  rank: C
        //  7: 51条2項又は53条2項（不正使用後の再出願）,
        //  rank: D
        //  8: 特25条（相互主義違反）,
        //  rank: E
        //  9: 6条（商品役務不明確又は区分違反）,
        //  rank: C
        //  10: その他
        //  rank: D
        //  99: 理由無
        //  rank: A

        $data = [
            [
                "id" => 1,
                'name' => '3条柱書き（使用意思）',
                'rank' => MLawsRegulation::RANK_B,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                "id" => 2,
                'name' => '3条柱書き（資格規制など）',
                'rank' => MLawsRegulation::RANK_C,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                "id" => 3,
                'name' => '3条1項各号（識別力）又は・及び4条1項16号（品質誤認）',
                'rank' => MLawsRegulation::RANK_D,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                "id" => 4,
                'name' => '4条1項1～7号（公益的理由）',
                'rank' => MLawsRegulation::RANK_D,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                "id" => 5,
                'name' => '4条1項8～19号（私益的理由、16号を除く）',
                'rank' => MLawsRegulation::RANK_D,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                "id" => 6,
                'name' => '8条（同日出願）',
                'rank' => MLawsRegulation::RANK_C,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                "id" => 7,
                'name' => '51条2項又は53条2項（不正使用後の再出願）',
                'rank' => MLawsRegulation::RANK_D,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                "id" => 8,
                'name' => '特25条（相互主義違反）',
                'rank' => MLawsRegulation::RANK_E,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                "id" => 9,
                'name' => '6条（商品役務不明確又は区分違反）',
                'rank' => MLawsRegulation::RANK_C_D,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                "id" => 10,
                'name' => 'その他',
                'rank' => MLawsRegulation::RANK_D,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                "id" => 99,
                'name' => '理由無',
                'rank' => MLawsRegulation::RANK_A,
                'created_at' => now(),
                'updated_at' => now()
            ],
        ];

        MLawsRegulation::insert($data);
    }
}
