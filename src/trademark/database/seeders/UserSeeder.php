<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Faker\Generator as Faker;
use Carbon\Carbon;

class UserSeeder extends Seeder
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
        $user = User::updateOrCreate(['name_trademark' => $this->faker->name], [
            'name_trademark' => $this->faker->name,
            'is_image_trademark' => $this->faker->numberBetween(0,1),
            'email' => 'user@example.com',
            'user_number' => $this->faker->numberBetween(11111,99999),
            'info_type_acc' => $this->faker->numberBetween(1,2),
            'info_name' => $this->faker->name,
            'info_name_furigana' => $this->faker->firstName(),
            'info_corporation_number' => random_int(100000, 999999),
            'info_nation_id' => $this->faker->numberBetween(1,20),
            'info_postal_code' => $this->faker->postcode,
            'info_prefectures_id' => $this->faker->numberBetween(1,20),
            'info_address_second' => $this->faker->address,
            'info_address_three' => $this->faker->address,
            'info_phone' => $this->faker->phoneNumber(),
            'info_member_id' => $this->faker->numberBetween(1,20),
            'password' => bcrypt('Test1234'),
            'info_gender' => $this->faker->numberBetween(1,2),
            'info_birthday' => Carbon::now()->subDays($this->faker->numberBetween(1, 30))->format('Y-m-d'),
            'info_question' => $this->faker->paragraph(),
            'info_answer' => $this->faker->paragraph(),
            'contact_type_acc' => $this->faker->numberBetween(1,2),
            'contact_name' => $this->faker->name,
            'contact_name_furigana' => $this->faker->name,
            'contact_name_department' => $this->faker->name,
            'contact_name_department_furigana' => $this->faker->name,
            'contact_name_manager' => $this->faker->name,
            'contact_name_manager_furigana' => $this->faker->name,
            'contact_nation_id' => $this->faker->numberBetween(1,20),
            'contact_postal_code' => $this->faker->postcode,
            'contact_prefectures_id' => $this->faker->numberBetween(1,20),
            'contact_address_second' => $this->faker->address,
            'contact_address_three' => $this->faker->address,
            'contact_phone' => $this->faker->phoneNumber(),
            'contact_email_second' => $this->faker->email,
            'contact_email_three' => $this->faker->companyEmail,
            'status' => $this->faker->numberBetween(1,2),
            'status_withdraw' => $this->faker->numberBetween(1,3),
            'reason_withdraw' => $this->faker->paragraph(),
            'problems' => $this->faker->name,
        ]);

        // $user->syncRoles(ADMIN_ROLE);
    }
}
