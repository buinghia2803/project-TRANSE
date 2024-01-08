<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(PermissionSeeder::class);
        $this->call(AdminSeeder::class);
        $this->call(NationSeeder::class);
        $this->call(PrefectureSeeder::class);
        $this->call(MPriceListDataSeeder::class);
        $this->call(SettingDataSeeder::class);
        $this->call(AgentDataSeeder::class);
        $this->call(MLawsRegulationSeeder::class);
        $this->call(MTypePlanSeeder::class);
        $this->call(MTypePlanDocSeeder::class);
        $this->call(MailTemplateSeeder::class);
    }
}
