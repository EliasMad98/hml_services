<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $contact_info = Setting::create([
            "key" => 'contact_info',
            "value" =>
            '{"phone":"0000000000","email": "hml@gmail.com"}'

        ]);
    }
}
