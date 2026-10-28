<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserController extends AdminController
{
    /** Restrict the whole controller to admins (defence in depth; route already gates). */
    public function index()
    {
        abort_unless($this->isAdmin(), 403);

        $me = new User;
        $me->forceFill($this->admin());

        return view('admin.users', [
            'rows' => User::orderBy('id')->get(),
            'user' => $this->admin(),
            'me' => $me,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless($this->isAdmin(), 403);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'username' => ['required', 'string', 'max:80', 'unique:users,username'],
            'email' => ['required', 'email', 'max:160'],
            'password' => ['required', 'confirmed', Password::min(8)],
            'role' => ['required', 'in:admin,editor'],
        ], [], [
            'name' => 'नाम', 'username' => 'उपयोगकर्ता नाम',
            'email' => 'ईमेल', 'password' => 'पासवर्ड', 'role' => 'भूमिका',
        ]);

        User::create([
            'name' => $data['name'],
            'username' => $data['username'],
            'email' => $data['email'],
            'password_hash' => bcrypt($data['password']),
            'role' => $data['role'],
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('admin.users')->with('ok', 'उपयोगकर्ता बनाया गया।');
    }

    public function update(Request $request, int|string $id): RedirectResponse
    {
        abort_unless($this->isAdmin(), 403);

        $user = User::findOrFail((int) $id);
        $me = $this->admin();

        // Never let the last admin demote or deactivate themselves.
        if ((int) $me['id'] === $id && ($request->input('role') !== 'admin' || ! $request->boolean('is_active'))) {
            return back()->with('err', 'आप अपनी स्वयं की भूमिका या सक्रिय स्थिति नहीं बदल सकते।');
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'username' => ['required', 'string', 'max:80', Rule::unique('users', 'username')->ignore($user->id)],
            'email' => ['required', 'email', 'max:160'],
            'password' => ['nullable', 'confirmed', Password::min(8)],
            'role' => ['required', 'in:admin,editor'],
        ], [], [
            'name' => 'नाम', 'username' => 'उपयोगकर्ता नाम',
            'email' => 'ईमेल', 'password' => 'पासवर्ड', 'role' => 'भूमिका',
        ]);

        $user->fill([
            'name' => $data['name'],
            'username' => $data['username'],
            'email' => $data['email'],
            'role' => $data['role'],
            'is_active' => $request->boolean('is_active'),
        ]);
        if (! empty($data['password'])) {
            $user->password_hash = bcrypt($data['password']);
        }
        $user->save();

        return redirect()->route('admin.users')->with('ok', 'उपयोगकर्ता अपडेट हुआ।');
    }

    public function destroy(int|string $id): RedirectResponse
    {
        abort_unless($this->isAdmin(), 403);

        if ((int) $this->admin()['id'] === $id) {
            return back()->with('err', 'आप अपना खाता नहीं हटा सकते।');
        }

        if (User::count() <= 1) {
            return back()->with('err', 'कम से कम एक उपयोगकर्ता रहना आवश्यक है।');
        }

        User::where('id', $id)->delete();

        return redirect()->route('admin.users')->with('ok', 'उपयोगकर्ता हटा दिया गया।');
    }
}
