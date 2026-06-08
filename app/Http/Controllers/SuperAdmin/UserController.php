<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::orderBy('role', 'desc')
            ->orderBy('nama', 'asc')
            ->get();

        return view('superadmin.user.index', compact('users'));
    }

    public function create()
    {
        return view('superadmin.user.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:100',
            'username' => 'required|string|max:50|unique:users,username',
            'password' => 'required|string|min:8',
            'role' => 'required|in:admin,super_admin',
        ], [
            'nama.required' => 'Nama wajib diisi.',
            'username.required' => 'Username wajib diisi.',
            'username.unique' => 'Username sudah digunakan.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 8 karakter.',
            'role.required' => 'Role wajib dipilih.',
        ]);

        User::create([
            'nama' => $request->nama,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'is_active' => $request->has('is_active') ? true : false,
        ]);

        return redirect()->route('superadmin.user.index')->with('success', 'User berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $editUser = User::findOrFail($id);

        return view('superadmin.user.edit', compact('editUser'));
    }

    public function update($id, Request $request)
    {
        $editUser = User::findOrFail($id);

        $request->validate([
            'nama' => 'required|string|max:100',
            'username' => 'required|string|max:50|unique:users,username,'.$id,
            'password' => 'nullable|string|min:8',
            'role' => 'required|in:admin,super_admin',
        ], [
            'nama.required' => 'Nama wajib diisi.',
            'username.required' => 'Username wajib diisi.',
            'username.unique' => 'Username sudah digunakan.',
            'password.min' => 'Password minimal 8 karakter.',
            'role.required' => 'Role wajib dipilih.',
        ]);

        $updateData = [
            'nama' => $request->nama,
            'username' => $request->username,
            'role' => $request->role,
            'is_active' => $request->has('is_active') ? true : false,
        ];

        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        $editUser->update($updateData);

        return redirect()->route('superadmin.user.index')->with('success', 'User berhasil diperbarui.');
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'id' => 'required|integer',
        ]);

        $currentId = Auth::id();
        if ((int) $request->id === (int) $currentId) {
            return back()->withErrors(['error' => 'Anda tidak boleh menonaktifkan akun sendiri.']);
        }

        $user = User::findOrFail($request->id);
        $user->update(['is_active' => false]);

        return redirect()->route('superadmin.user.index')->with('success', 'User berhasil dinonaktifkan.');
    }
}
