<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;


class UserRepository
{
    public function create($data)
    {
        $path = (isset($data['image'])&&$data['image']->isValid()?$data['image']->store('profile','public'):null);
       $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'image'=> $path,
            'password' => Hash::make($data['password']),
        ]);
        $user->assignRole($data['role']);
        return $user;

    }
    public function update($data,$user,$from_profile=false)
    {
        $user->name= $data['name'];
        $user->email= $data['email'];
        if(isset($data['password']))
        {
            $user->password= Hash::make($data['password']);
        }
        if(isset($data['image']) && $data['image']->isValid())
        {
            //delete old file
            if($user->image)
            {
                Storage::disk('public')->delete($user->image);
            }
            //store new file
            $path = $data['image']->store('profile','public');
            $user->image=$path;
        }
        $user->save();
        if(!$from_profile){
            if($data['role']){
                $user->syncRoles($data['role']);
            }
        }
        return $user;
    }
    public function delete($data,$user)
    {
        $user->delete();
        if($user->image)
        {
            Storage::disk('public')->delete($user->image);
        }
    }
   
    public function updatePassword($data,$user)
    {
        $user->password = Hash::make($data['newpassword']);
        $user->save();
        return $user;
    }
 }
