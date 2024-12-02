<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Building;
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
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        $this->call(UserSeeder::class);
        $this->call(TenantSeeder::class);
        $this->call(NonTenantSeeder::class);
        $this->call(ServiceSeeder::class);
        $this->call(BuildingSeeder::class);
        $this->call(ApartmentSeeder::class);
        $this->call(AddressSeeder::class);
        $this->call(RolesAndPermissionsSeeder::class);
        $this->call(JobTypeSeeder::class);
        $this->call(EmployeeSeeder::class);
        $this->call(SettingSeeder::class);

    }
}
