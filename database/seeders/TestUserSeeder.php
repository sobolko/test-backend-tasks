<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Spatie\Permission\Models\Role;

class TestUserSeeder extends Seeder
{
    public function run(): void
    {
        // Создать роли, если не существуют
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $memberRole = Role::firstOrCreate(['name' => 'member']);

        $admin = User::updateOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => Hash::make('password'),
            ]
        );
        $admin->assignRole('admin');

        $member = User::updateOrCreate(
            ['email' => 'ivan.petrovich@example.com'],
            [
                'name' => 'Ivan Petrovich',
                'password' => Hash::make('password'),
            ]
        );
        $member->assignRole('member');
    }
}