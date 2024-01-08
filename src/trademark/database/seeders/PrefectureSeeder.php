<?php

namespace Database\Seeders;

use App\Models\MNation;
use App\Models\MPrefecture;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class PrefectureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        DB::table('m_prefectures')->truncate();
        Schema::enableForeignKeyConstraints();

        $prefectures = config('prefectures');
        foreach ($prefectures as $nationID => $prefecture) {
            if ($nationID == NATION_JAPAN_ID) {
                foreach ($prefecture as $id => $name) {
                    MPrefecture::create([
                        'id' => $id,
                        'm_nation_id' => $nationID,
                        'name' => $name,
                    ]);
                }
            }
        }

        //create prefectures fake of nations no-japan
        $nationsNoJP = MNation::where('id', '!=', NATION_JAPAN_ID)->get();
        foreach ($nationsNoJP as $item) {
            MPrefecture::create([
                'm_nation_id' => $item->id,
                'name' => Str::random(4),
            ]);
        }

    }
}
