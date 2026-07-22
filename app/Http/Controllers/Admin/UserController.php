<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function index()
    {
        $users = User::orderBy('name')->paginate(20);
        return view('admin.users', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'bio_id' => 'nullable|string|max:50',
            'tag' => 'required|in:1,2',
            'password' => 'nullable|string|min:6',
        ]);

        $password = $request->password ?: 'oat' . date('Y');

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'bio_id' => $request->bio_id,
            'tag' => $request->tag,
            'password' => $password,
            'is_admin' => $request->has('is_admin'),
        ]);

        return back()->with('success', "Employee registered successfully. Default password: {$password}");
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'bio_id' => 'nullable|string|max:50',
            'tag' => 'required|in:1,2',
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'bio_id' => $request->bio_id,
            'tag' => $request->tag,
            'is_admin' => $request->has('is_admin'),
        ]);

        return back()->with('success', 'Employee updated successfully.');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return back()->with('success', 'Employee removed successfully.');
    }

    public function resetPassword(User $user)
    {
        $newPassword = 'oat' . date('Y');
        $user->update(['password' => $newPassword]);

        return back()->with('success', "Password for {$user->name} has been reset to: {$newPassword}");
    }
}
