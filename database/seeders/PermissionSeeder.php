<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Permission::firstOrCreate(['guard_name' => 'web','name' => 'view dashboard']);
        Permission::firstOrCreate(['guard_name' => 'web','name' => 'view counters']);
        Permission::firstOrCreate(['guard_name' => 'web','name' => 'view services']);
        Permission::firstOrCreate(['guard_name' => 'web','name' => 'view users']);
        Permission::firstOrCreate(['guard_name' => 'web','name' => 'call token']);
        Permission::firstOrCreate(['guard_name' => 'web','name' => 'view settings']);
        Permission::firstOrCreate(['guard_name' => 'web','name' => 'view reports']);
        Permission::firstOrCreate(['guard_name' => 'web','name' => 'view user_roles']);
        Permission::firstOrCreate(['guard_name' => 'web','name' => 'issue token']);
        Permission::firstOrCreate(['guard_name' => 'web','name' => 'view display']);
        Permission::firstOrCreate(['guard_name' => 'web','name' => 'view profile']);
    }
}
