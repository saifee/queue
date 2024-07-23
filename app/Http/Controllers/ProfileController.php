<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Repositories\UserRepository;

class ProfileController extends Controller
{
    public $users;

    public function __construct(UserRepository $users)
    {
        $this->users = $users;
    }

    public function profile(Request $request)
    {
        $profile = Auth::user();
        return view('profile.index')->with('profile', $profile);
    }
    public function update(Request $request)
    {
        $user = Auth::user();
        $validated = $request->validate([
            'name' => 'required|min:2',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'image' => 'mimes:jpg,jpeg,png,gif|max:2048'
        ]);
        DB::beginTransaction();
        try {
            $user = $this->users->update($request->all(), $user, true);
        } catch (\Exception $e) {
            DB::rollback();
            $request->session()->flash('error', 'Something Went Wrong');
            return redirect()->route('profile');
        }
        DB::commit();
        $request->session()->flash('success', 'Updated successfully');
        return redirect()->route('profile');
    }
    public function changePassword(Request $request)
    {
        $user = Auth::user();
        $request->validate([
            'newpassword' => 'required',
            'confirmpassword' => 'required',
        ]);

        DB::beginTransaction();
        try {
            $user = $this->users->updatePassword($request->all(), $user);
        } catch (\Exception $e) {
            DB::rollback();
            $request->session()->flash('error', 'Something Went Wrong');
            return redirect()->route('profile')->with('password_form', 'true');
        }
        DB::commit();
        $request->session()->flash('success', 'Password updated successfully');
        return redirect()->back()->withInput($request->only('password_form'));
    }
}
