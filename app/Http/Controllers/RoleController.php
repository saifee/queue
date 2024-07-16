<?php

namespace App\Http\Controllers;

use App\Repositories\RollRepository;
use App\Repositories\TokenRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    protected $roleRepository;

    public function __construct(RollRepository $roleRepository)
    {
        $this->roleRepository = $roleRepository;
    }

    public function index()
    {
        return view('roles.index', ['roles' => Role::get()]);
    }

    public function create()
    {
        return view('roles.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
        ]);

        DB::beginTransaction();
        try {
            $res = $this->roleRepository->create($request->all());
        } catch (\Exception $e) {
            DB::rollback();
            $request->session()->flash('error', 'something went wrong');
            return redirect()->route('rolls.index');
        }
        DB::commit();
        $request->session()->flash('success', 'Succesfully Added Role');
        return
            redirect()->route('roles.index');
    }

    public function edit(Role $role)
    {
        return view('roles.edit', [
            'role' => $role,
            'permissions' => $role->permissions->pluck('name')->toArray()
        ]);
    }

    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => 'required',
        ]);

        DB::beginTransaction();
        try {
            $res = $this->roleRepository->update($request->all(), $role);
        } catch (\Exception $e) {
            DB::rollback();
            $request->session()->flash('error', 'something went wrong');
            return redirect()->route('rolls.index');
        }
        DB::commit();
        $request->session()->flash('success', 'Succesfully Updated Role');
        return redirect()->route('roles.index');
    }

    public function destroy(Role $role, Request $request)
    {

        DB::beginTransaction();
        try {
            $role = $this->roleRepository->delete($request->all(), $role);
        } catch (\Exception $e) {
            DB::rollback();
            $request->session()->flash('error', 'Something Went Wrong');
            return redirect()->route('roles.index');
        }
        DB::commit();
        $request->session()->flash('success', 'Succesfully deleted the record');
        return redirect()->route('roles.index');
    }
}
