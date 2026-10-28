<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (session('admin')) {
            return redirect()->route('admin.dashboard');
        }

        return view('admin.login', ['err' => null]);
    }

    /**
     * Session-based admin login. The legacy users table uses `password_hash`,
     * and the session payload is the raw row (minus the hash) as before.
     */
    public function login(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'username' => ['required', 'string', 'max:80'],
            'password' => ['required', 'string', 'max:255'],
        ], [
            'username.required' => 'उपयोगकर्ता नाम दर्ज करें।',
            'password.required' => 'पासवर्ड दर्ज करें।',
        ]);

        $user = User::where('username', $data['username'])->first();

        if (! $user || ! $user->is_active || ! Hash::check($data['password'], $user->password_hash)) {
            throw ValidationException::withMessages([
                'username' => 'उपयोगकर्ता नाम या पासवर्ड गलत है।',
            ]);
        }

        // Same session payload shape the legacy panel used.
        $payload = [
            'id' => $user->id,
            'name' => $user->name,
            'username' => $user->username,
            'email' => $user->email,
            'role' => $user->role,
            'avatar' => $user->avatar,
        ];

        // Prevent fixation: clean id, then re-establish.
        $request->session()->invalidate();
        $request->session()->put('admin', $payload);
        $request->session()->regenerate();

        return redirect()->route('admin.dashboard');
    }

    public function logout(Request $request): RedirectResponse
    {
        $request->session()->forget('admin');
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}
