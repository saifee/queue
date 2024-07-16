<?php

namespace Database\Seeders;

use App\Models\CallStatus;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CallStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $call_statuses = [
            [
                'id' => 1,
                'name' => 'served',
            ],
            [
                'id' => 2,
                'name' => 'noshow',
            ]
        ];

        foreach ( $call_statuses as  $call_status) {
            $data =  CallStatus::find($call_status['id']);
            if($data) 
            {
                $data->name = $call_status['name'];
                $data->save();
            }
            else CallStatus::create($call_status);
            
    }
    }
}
