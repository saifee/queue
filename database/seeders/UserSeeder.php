<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = [
            [
                'id' => 1,
                'name' => 'admin',
                'email' => 'admin@mail.com',
                'password' =>  Hash::make('123456')
            ]
        ];
        $roles = Role::find(1);
        foreach ($users as $user) {
            $data =  User::find($user['id']);
            if (!$data) {
                $data = User::create($user);
                $data->syncRoles($roles);
            }

        }
    }
}
