<?php

namespace Database\Seeders;

use App\Models\MNation;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class NationSeeder extends Seeder
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
        DB::table('m_nations')->truncate();
        Schema::enableForeignKeyConstraints();

        $nations = config('nations');
        foreach ($nations as $id => $name) {
            MNation::create([
                'id' => $id,
                'name' => $name
            ]);
        }
    }
}
