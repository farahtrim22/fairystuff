<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Buat / perbarui user admin untuk login Filament
        User::updateOrCreate(
            ['email' => 'tri@gmail.com'],
            [
                'name' => 'Admin',
                // Dengan cast 'password' => 'hashed', kita boleh isi plain text
                'password' => 'secret123',
            ]
        );
    }
}