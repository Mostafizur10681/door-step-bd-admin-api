<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\CustomerProfile;
use App\Models\AdminProfile;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /**
     * Register a new Customer.
     */
    public function registerCustomer(Request $request): JsonResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', 'min:6'],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'role' => 'customer',
            'status' => 'active',
        ]);

        // Assign Customer role (create if not exists)
        $role = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'Customer', 'guard_name' => 'web']);
        $user->assignRole($role);

        // Store customer details in customer_profiles table
        CustomerProfile::firstOrCreate(
            ['user_id' => $user->id],
            ['shipping_address' => $request->address]
        );

        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->success([
            'user' => $user->load('roles', 'customerProfile'),
            'access_token' => $token,
            'token' => $token,
            'token_type' => 'Bearer',
        ], 'Customer registration successful', 201);
    }

    /**
     * Register a new Seller.
     */
    public function registerSeller(Request $request): JsonResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'vendor',
            'status' => 'active',
        ]);

        // Assign Seller role (create if not exists)
        $role = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'Seller', 'guard_name' => 'web']);
        $user->assignRole($role);

        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->success([
            'user' => $user->load('roles'),
            'access_token' => $token,
            'token_type' => 'Bearer',
        ], 'Seller registration successful', 201);
    }

    /**
     * Register a new Admin.
     */
    public function registerAdmin(Request $request): JsonResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'phone' => ['nullable', 'string', 'max:20'],
            'designation' => ['nullable', 'string', 'max:255'],
            'department' => ['nullable', 'string', 'max:255'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'role' => 'admin',
            'status' => 'active',
        ]);

        // Assign Admin role (create if not exists)
        $role = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        $user->assignRole($role);

        // Store admin details in admin_profiles table
        $adminProfile = AdminProfile::create([
            'user_id' => $user->id,
            'designation' => $request->designation,
            'department' => $request->department,
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->success([
            'user' => $user->load('roles', 'adminProfile'),
            'access_token' => $token,
            'token_type' => 'Bearer',
        ], 'Admin registration successful', 201);
    }

    /**
     * Log in a Customer.
     */
    public function loginCustomer(Request $request): JsonResponse
    {
        $request->validate([
            'email' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('email', $request->email)
            ->orWhere('phone', $request->email)
            ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return $this->error('Invalid login credentials', [
                'email' => ['The provided credentials do not match our records.']
            ], 401);
        }

        if ($user->status === 'blocked') {
            throw ValidationException::withMessages([
                'status' => ['Your account has been blocked.'],
            ]);
        }

        // Ensure customer role exists
        if (!$user->hasRole('Customer')) {
            $role = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'Customer', 'guard_name' => 'web']);
            $user->assignRole($role);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->success([
            'user' => $user->load('roles.permissions', 'permissions', 'customerProfile'),
            'access_token' => $token,
            'token' => $token,
            'token_type' => 'Bearer',
        ], 'Customer login successful');
    }

    /**
     * Log in a Seller.
     */
    public function loginSeller(Request $request): JsonResponse
    {
        $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return $this->error('Invalid login credentials', [
                'email' => ['The provided credentials do not match our records.']
            ], 401);
        }

        if ($user->status === 'blocked') {
            throw ValidationException::withMessages([
                'status' => ['Your account has been blocked.'],
            ]);
        }

        // Verify the user has Seller role
        if (!$user->hasRole('Seller')) {
            return $this->error('Unauthorized role access', [
                'role' => ['You are not authorized to login as a seller.']
            ], 403);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->success([
            'user' => $user->load('roles.permissions', 'permissions'),
            'access_token' => $token,
            'token_type' => 'Bearer',
        ], 'Seller login successful');
    }

    /**
     * Log in an Admin.
     */
    public function loginAdmin(Request $request): JsonResponse
    {
        $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return $this->error('Invalid login credentials', [
                'email' => ['The provided credentials do not match our records.']
            ], 401);
        }

        if ($user->status === 'pending') {
            throw ValidationException::withMessages([
                'status' => ['Your account is pending admin approval.'],
            ]);
        }

        if ($user->status === 'blocked') {
            throw ValidationException::withMessages([
                'status' => ['Your account has been blocked.'],
            ]);
        }

        // Verify the user has Admin or Editor role
        if (!$user->hasRole(['Admin', 'Editor'])) {
            return $this->error('Unauthorized role access', [
                'role' => ['You are not authorized to login as an admin.']
            ], 403);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->success([
            'user' => $user->load('roles.permissions', 'permissions', 'adminProfile'),
            'access_token' => $token,
            'token_type' => 'Bearer',
        ], 'Admin login successful');
    }

    /**
     * Register a new user.
     */
    public function register(Request $request): JsonResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'customer',
            'status' => 'active',
        ]);

        // Assign default Customer role if it exists
        if (\Spatie\Permission\Models\Role::where('name', 'Customer')->exists()) {
            $user->assignRole('Customer');
        }

        CustomerProfile::create([
            'user_id' => $user->id,
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->success([
            'user' => $user->load('roles', 'customerProfile'),
            'access_token' => $token,
            'token_type' => 'Bearer',
        ], 'Registration successful', 201);
    }

    /**
     * Log in a user.
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return $this->error('Invalid login credentials', [
                'email' => ['The provided credentials do not match our records.']
            ], 401);
        }

        if ($user->status === 'blocked') {
            throw ValidationException::withMessages([
                'status' => ['Your account has been blocked.'],
            ]);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->success([
            'user' => $user->load('roles.permissions', 'permissions', 'customerProfile', 'adminProfile'),
            'access_token' => $token,
            'token_type' => 'Bearer',
        ], 'Login successful');
    }

    /**
     * Log out a user (revoke token).
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return $this->success([], 'Logged out successfully');
    }

    /**
     * Get the authenticated user's profile.
     */
    public function profile(Request $request): JsonResponse
    {
        $user = $request->user()->load('roles.permissions', 'permissions', 'customerProfile');
        return $this->success([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone ?? '',
            'address' => $user->address ?? $user->customerProfile?->shipping_address ?? '',
            'avatar' => $user->profile_pic ?? '',
            'profile_pic' => $user->profile_pic ?? '',
            'role' => $user->role,
            'roles' => $user->roles,
        ], 'Profile retrieved successfully');
    }

    /**
     * Update the authenticated user's profile.
     */
    public function updateProfile(Request $request): JsonResponse
    {
        $user = $request->user();
        if (!$user && $token = $request->bearerToken()) {
            $model = \Laravel\Sanctum\PersonalAccessToken::findToken($token);
            if ($model && $model->tokenable instanceof \App\Models\User) {
                $user = $model->tokenable;
            }
        }

        if (!$user) {
            return $this->error('Unauthenticated. Please sign in again.', [], 401);
        }

        $request->validate([
            'name' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'phone' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:1000'],
            'avatar' => ['nullable', 'string'],
            'profile_pic' => ['nullable', 'string'],
        ]);

        $userUpdate = [];
        if ($request->has('name') && !empty($request->name)) {
            $userUpdate['name'] = $request->name;
        }
        if ($request->has('email') && !empty($request->email)) {
            $userUpdate['email'] = $request->email;
        }
        if ($request->has('phone')) {
            $userUpdate['phone'] = $request->phone;
        }
        if ($request->has('address')) {
            $userUpdate['address'] = $request->address;
        }
        if ($request->has('avatar')) {
            $userUpdate['profile_pic'] = $request->avatar;
        } elseif ($request->has('profile_pic')) {
            $userUpdate['profile_pic'] = $request->profile_pic;
        }

        if (!empty($userUpdate)) {
            $user->update($userUpdate);
        }

        if ($request->has('address')) {
            CustomerProfile::updateOrCreate(
                ['user_id' => $user->id],
                ['shipping_address' => $request->address]
            );
        }

        $user->refresh()->load('roles.permissions', 'permissions', 'customerProfile');

        return $this->success([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone ?? '',
            'address' => $user->address ?? $user->customerProfile?->shipping_address ?? '',
            'avatar' => $user->profile_pic ?? '',
            'profile_pic' => $user->profile_pic ?? '',
            'role' => $user->role,
        ], 'Profile updated successfully');
    }

    /**
     * Change the authenticated user's password.
     */
    public function changePassword(Request $request): JsonResponse
    {
        $user = $request->user();
        if (!$user && $token = $request->bearerToken()) {
            $model = \Laravel\Sanctum\PersonalAccessToken::findToken($token);
            if ($model && $model->tokenable instanceof \App\Models\User) {
                $user = $model->tokenable;
            }
        }

        if (!$user) {
            return $this->error('Unauthenticated. Please sign in again.', [], 401);
        }

        $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:6'],
        ]);

        if (!Hash::check($request->current_password, $user->password)) {
            return $this->error('The current password provided is incorrect.', [
                'current_password' => ['The current password provided is incorrect.']
            ], 422);
        }

        if ($request->has('password_confirmation') && $request->password !== $request->password_confirmation) {
            return $this->error('The new password confirmation does not match.', [
                'password_confirmation' => ['The new password confirmation does not match.']
            ], 422);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return $this->success([], 'Password changed successfully');
    }

    /**
     * Delete the authenticated user's account.
     */
    public function deleteAccount(Request $request): JsonResponse
    {
        $user = $request->user();
        if (!$user && $token = $request->bearerToken()) {
            $model = \Laravel\Sanctum\PersonalAccessToken::findToken($token);
            if ($model && $model->tokenable instanceof \App\Models\User) {
                $user = $model->tokenable;
            }
        }

        if (!$user) {
            return $this->error('Unauthenticated. Please sign in again.', [], 401);
        }

        try {
            $user->tokens()->delete();
            if ($user->customerProfile) {
                $user->customerProfile->delete();
            }
            if ($user->adminProfile) {
                $user->adminProfile->delete();
            }
            if (method_exists($user, 'wishlists')) {
                $user->wishlists()->delete();
            }
            if (method_exists($user, 'addresses')) {
                $user->addresses()->delete();
            }
            $user->delete();

            return $this->success([], 'Account deleted successfully.');
        } catch (\Exception $e) {
            return $this->error('Failed to delete account: ' . $e->getMessage(), [], 500);
        }
    }

    /**
     * Send password reset link/token.
     */
    public function forgotPassword(Request $request): JsonResponse
    {
        $request->validate(['email' => 'required|email']);

        // We use Laravel's native Password broker which generates reset tokens.
        // It will send the notification/email which is caught in storage/logs/laravel.log.
        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            return $this->success([], __($status));
        }

        return $this->error(__($status), [
            'email' => [__($status)]
        ], 400);
    }

    /**
     * Reset the user's password using reset token.
     */
    public function resetPassword(Request $request): JsonResponse
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:8',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return $this->success([], __($status));
        }

        return $this->error(__($status), [
            'email' => [__($status)]
        ], 400);
    }
}
