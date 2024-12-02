<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Service;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Service::create([
            'title'=>"Air Conditioner",
            'image_path'=>'images/services/air.jpg'
        ]);
        Service::create([
            'title'=>"Carpet Cleaning",
            'image_path'=>'images/services/carpetcleaning.png'
        ]);
        Service::create([
            'title'=>"Cleaning",
            'image_path'=>'images/services/cleaning.png'
        ]);
        Service::create([
            'title'=>"Deckand Patio",
            'image_path'=>'images/services/deckand_patio.png'
        ]);
        Service::create([
            'title'=>"Electric",
            'image_path'=>'images/services/electrician.png'
        ]);
        Service::create([
            'title'=>"Flooring",
            'image_path'=>'images/services/flooring.png'
        ]);
        Service::create([
            'title'=>"Plumbing",
            'image_path'=>'images/services/plumbing.png'
        ]);
        Service::create([
            'title'=>"Handyman",
            'image_path'=>'images/services/handyman.png'
        ]);
    }
}
