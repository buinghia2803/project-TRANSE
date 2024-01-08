<?php

namespace Database\Seeders;

use App\Models\MTypePlanDoc;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Faker\Generator as Faker;

class MTypePlanDocSeeder extends Seeder
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
        DB::table('m_type_plan_docs')->truncate();
        Schema::enableForeignKeyConstraints();

        $data = [
            [
                'm_type_plan_id' => 2,
                'name' => '使用意思確認書面（出願人が記載の商品・役務について事業予定があり、商標を使用する意思があることを示した書面です）',
                'description' => '全空欄の日付記入をお願い申し上げます。
記入の上、Word・PDF・jpegのいずれかでご返送お願いします。',
                'url' => '/common/files/A-203：使用意思確認書面（３条１項柱書）.doc',
            ],
            [
                'm_type_plan_id' => 2,
                'name' => '事業予定(出願人の事業予定を示した書面です)',
                'description' => '全空欄の日付記入をお願い申し上げます。
記入の上、Word・PDF・jpegのいずれかでご返送お願いします。',
                'url' => '/common/files/A-203：事業予定（３条１項柱書）.doc',
            ],
            [
                'm_type_plan_id' => 4,
                'name' => '御社の提供する商品（役務）を表すものとして需要者・取引者に認知されている事実を証明するための証拠となる資料です。
具体的には、新聞・雑誌の記事、チラシ・ホームページなどの印刷物になります。',
                'description' => 'jpg又はpdfにてご返送お願いします。',
                'url' => null,
            ],
            [
                'm_type_plan_id' => 5,
                'name' => '承諾書（日付、承諾者様の記入等をお願いします）',
                'description' => '左欄よりダウンロードしたフォームに上記の内容を記入の上、jpg又はpdfにてご返送お願いします。',
                'url' => '/common/files/A-203：承諾書（４条１項８号）.doc',
            ],
            [
                'm_type_plan_id' => 7,
                'name' => '商品（役務）の内容が分かる書面',
                'description' => 'jpg又はpdfにてご返送お願いします。',
                'url' => null,
            ],
            [
                'm_type_plan_id' => 8,
                'name' => '資格を有することを証明する書面',
                'description' => null,
                'url' => null,
            ],
            [
                'm_type_plan_id' => 8,
                'name' => '新聞・雑誌の記事、チラシ・ホームページなどの印刷物',
                'url' => null,
                'description' => null,
            ],
            [
                'm_type_plan_id' => 8,
                'name' => '使用意思（フォーマット付き）',
                'description' => null,
                'url' => null,
            ],
            [
                'm_type_plan_id' => 8,
                'name' => '承諾書（フォーマット付き）',
                'description' => null,
                'url' => null,
            ],
            [
                'm_type_plan_id' => 8,
                'name' => '自由記述',
                'description' => null,
                'url' => null,
            ],
            [
                'm_type_plan_id' => 8,
                'name' => '不要',
                'description' => null,
                'url' => null,
            ],
        ];

        MTypePlanDoc::insert($data);
    }
}
