<?php

namespace Database\Seeders;

use App\Models\JobType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class JobTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
            JobType::create([
            'name'=>"Cleaning" ]);
            JobType::create([
            'name'=>"Electric" ]);
            JobType::create([
            'name'=>"Plumbing" ]);
            JobType::create([
            'name'=>"Air Conditioner" ]);
    }
}
