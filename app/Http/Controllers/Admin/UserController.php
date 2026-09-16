<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\AdminProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $admins = User::where('role', 'admin')->with('adminProfile')->latest()->paginate(15);
        $pendingAdmins = User::where('role', 'admin')->where('status', 'pending')->with('adminProfile')->get();

        return view('admin.users.index', compact('admins', 'pendingAdmins'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|max:30|unique:users,phone',
            'department' => 'nullable|string|max:100',
            'password' => 'required|string|min:6',
            'status' => 'required|in:active,pending,blocked',
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'password' => Hash::make($data['password']),
            'role' => 'admin',
            'status' => $data['status'],
        ]);

        AdminProfile::create([
            'user_id' => $user->id,
            'department' => $data['department'] ?? 'Administration',
        ]);

        return redirect()->route('admin.users.index')->with('success', 'Admin user created successfully!');
    }

    public function updateStatus(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $status = $request->input('status', 'active');
        if (in_array($status, ['active', 'pending', 'blocked'])) {
            $user->status = $status;
            $user->save();
        }

        if ($request->isMethod('get')) {
            return redirect()->route('admin.users.index')->with('success', 'User status updated to ' . $user->status);
        }

        return back()->with('success', 'User status updated to ' . $user->status);
    }

    public function approve($id)
    {
        $user = User::findOrFail($id);
        $user->status = 'active';
        $user->save();

        return back()->with('success', 'User account approved!');
    }

    public function reject($id)
    {
        $user = User::findOrFail($id);
        $user->status = 'blocked';
        $user->save();

        return back()->with('success', 'User account rejected and blocked.');
    }

    public function toggleBlock($id)
    {
        $user = User::findOrFail($id);
        $user->status = $user->status === 'blocked' ? 'active' : 'blocked';
        $user->save();

        return back()->with('success', 'User status updated to ' . $user->status);
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own logged-in account.');
        }

        if ($user->adminProfile) {
            $user->adminProfile->delete();
        }
        if ($user->customerProfile) {
            $user->customerProfile->delete();
        }
        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully!');
    }
}
