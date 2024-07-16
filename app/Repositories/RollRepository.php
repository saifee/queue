<?php

namespace App\Repositories;

use Spatie\Permission\Models\Role;

class RollRepository
{

    public function create($data)
    {
        $data['permissions'] = array_keys($data['permission']);
        $role = Role::create(['name' => $data['name']]);
        $role->givePermissionTo($data['permissions']);
    }

    public function update($data, $role)
    {
        $data['permissions'] = array_keys($data['permission']);
        $role->name = $data['name'];
        $role->save();
        $role->syncPermissions($data['permissions']);
    }

    public function delete($data, $role)
    {
        $role->delete();
    }
}
