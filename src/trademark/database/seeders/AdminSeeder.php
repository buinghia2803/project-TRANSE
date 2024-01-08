<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Role;
use Faker\Generator as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AdminSeeder extends Seeder
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
        DB::table('admins')->truncate();
        DB::table('model_has_roles')->truncate();
        Schema::enableForeignKeyConstraints();

        $admins = [
            [
                'name' => 'Jimu',
                'role' => ROLE_OFFICE_MANAGER,
                'email' => 'jimu@admin.com',
                'admin_number' => 'jimu1234',
                'password' => bcrypt('Test1234'),
            ],
            [
                'name' => 'Seki',
                'role' => ROLE_SUPERVISOR,
                'email' => 'seki@admin.com',
                'admin_number' => 'seki1234',
                'password' => bcrypt('Test1234'),
            ],
            [
                'name' => 'Tantou',
                'role' => ROLE_MANAGER,
                'email' => 'tantou@admin.com',
                'admin_number' => 'tantou1234',
                'password' => bcrypt('Test1234'),
            ]
        ];

        $roles = Role::all();

        foreach ($admins as $admin) {
            $admin = Admin::create($admin);

            $role = $roles->where('id', $admin['role'])->first();
            $admin->syncRoles($role);
        }
    }
}
