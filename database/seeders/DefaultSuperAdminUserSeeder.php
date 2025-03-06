<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DefaultSuperAdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::create([
            'name' => 'Super Admin',
            'email' => 'super@admin.com',
            'password' => 'superadmin',
        ]);

        $user->markEmailAsVerified();

        $user->assignRole('Super Admin');

    }
}
