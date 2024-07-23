<?php

namespace Database\Seeders;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $s = Setting::find(1);
        if(!$s){
            $settings = [
                'id' => 1,
                'language_id'=>1,
                'name' => 'company',
                'address' => 'company',
                'email' => 'company@company.com',
                'location' =>  'location',
                'timezone' => config('app.timezone'),
                'sms_enabled' => false,
        ];

        Setting::create($settings);
        }
    }
}
