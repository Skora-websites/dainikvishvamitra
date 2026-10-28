<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Admin/editor accounts match the legacy install so existing logins keep working.
        $this->ensureUser('प्रशासक', 'admin', 'admin@vishwamitra.in', 'admin', 'admin');
        $this->ensureUser('संपादक', 'editor', 'editor@vishwamitra.in', 'editor', 'editor');

        $this->call(LegacyImportSeeder::class);
    }

    private function ensureUser(string $name, string $username, string $email, string $password, string $role): void
    {
        User::updateOrCreate(
            ['username' => $username],
            ['name' => $name, 'email' => $email, 'role' => $role, 'password_hash' => Hash::make($password), 'is_active' => true]
        );
    }
}
