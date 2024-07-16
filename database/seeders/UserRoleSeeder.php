<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roles = [
            [
                'id' => 1,
                'guard_name' => 'web',
                'name' => 'Super-Admin',
            ]
        ];

        foreach ($roles as $role) {
            $data =  Role::find($role['id']);
            if (!$data) {
                Role::create($role);
            }
        }
    }
}
