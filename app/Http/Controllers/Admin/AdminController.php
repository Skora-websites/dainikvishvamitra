<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

/**
 * Shared behaviour for all admin controllers: the logged-in admin payload
 * (legacy row shape) and a common redirect-on-error helper.
 */
abstract class AdminController extends Controller
{
    /** @return array<string,mixed> */
    protected function admin(): array
    {
        return session('admin') ?? [];
    }

    protected function isAdmin(): bool
    {
        return ($this->admin()['role'] ?? '') === 'admin';
    }
}
