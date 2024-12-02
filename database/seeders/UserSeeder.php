<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\User::factory()->count(100)->create();
        // $user=  User::create([
        //     'first_name'=>"admin",
        //     'last_name'=>"admin",
        //     'email'=>"admin@gmail.com",
        //     'phone' =>"0935121682",
        //     'password'=>bcrypt("12345678"),
        // ]);
    }
}
