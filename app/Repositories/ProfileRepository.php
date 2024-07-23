<?php

namespace App\Repositories;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;


class ProfileRepository
{
    public function create($data)
    {
        $path = (isset($data['image']) && $data['image']->isValid() ? $data['image']->store('profile', 'public') : null);
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'image' => $path,
            'password' => Hash::make($data['password']),
        ]);
        $user->assignRole($data['role']);
        return $user;
    }
    public function update($data, $admin)
    {
        $admin->name = $data['name'];
        $admin->email = $data['email'];
        if (isset($data['password'])) {
            $admin->password = Hash::make($data['password']);
        }
        if (isset($data['image']) && $data['image']->isValid()) {
            //delete old file
            if ($admin->image) {
                Storage::disk('public')->delete($admin->image);
            }
            //store new file
            $path = $data['image']->store('profile', 'public');
            $admin->image = $path;
        }
        if (isset($data['role'])) {
            $admin->syncRoles($data['role']);
        }
        $admin->save();
        return $admin;
    }
    public function delete($data, $admin)
    {
        $admin->delete();
        if ($admin->image) {
            Storage::disk('public')->delete($admin->image);
        }
    }

    public function updatePassword($data, $admin)
    {
        $admin->password = Hash::make($data['newpassword']);
        $admin->save();
        return $admin;
    }
}
