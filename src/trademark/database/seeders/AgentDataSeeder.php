<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Agent;
use App\Models\AgentGroup;
use App\Models\AgentGroupMap;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Faker\Generator as Faker;
use Carbon\Carbon;

class AgentDataSeeder extends Seeder
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
        DB::table('agents')->truncate();
        DB::table('agent_groups')->truncate();
        DB::table('agent_group_maps')->truncate();
        Schema::enableForeignKeyConstraints();

        $this->adminId = Admin::all()->pluck('id')->toArray();
        $identificationNumbers = ['１４２７８５６３９','５２６９１４７８３', '７８６１３９４５２'];
        $arrayName = ['テスト太郎','テスト太郎', 'テスト太郎'];
        $depositAccountNums = ['１４２８５６','５２６９１４', '７８６１３９'];

        for ($i=0; $i < 3; $i++) {
            $adminID = $this->adminId[rand(0,count($this->adminId) - 1)];
            $agent = Agent::create([
                'admin_id' => $adminID,
                'identification_number' => $identificationNumbers[$i],
                'name' => $arrayName[$i],
                'deposit_account_number' => $depositAccountNums[$i],
                'deposit_type' => $this->faker->numberBetween(1,2),
            ]);

            $agentGroup = AgentGroup::create([
                'admin_id' => $adminID,
                'name' => $this->faker->name,
                'status_choice' => !$i,
            ]);
            AgentGroupMap::create([
                'agent_group_id' => $agentGroup->id,
                'agent_id' => $agent->id,
            ]);
        }
    }
}
