<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AdminUserService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    protected AdminUserService $adminUserService;

    public function __construct(AdminUserService $adminUserService)
    {
        $this->adminUserService = $adminUserService;
    }

    public function index()
    {
        $users = $this->adminUserService->getAllUsers();
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $this->adminUserService->createUser($request->all());
        return redirect()->route('admin.users.index')->with('success', 'User created');
    }

    public function edit($id)
    {
        return view('admin.users.edit', compact('id'));
    }

    public function update(Request $request, $id)
    {
        $this->adminUserService->updateUser($id, $request->all());
        return redirect()->route('admin.users.index')->with('success', 'User updated');
    }

    public function destroy($id)
    {
        $this->adminUserService->deleteUser($id);
        return redirect()->route('admin.users.index')->with('success', 'User deleted');
    }
}
