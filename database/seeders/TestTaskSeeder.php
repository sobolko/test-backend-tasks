<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Task;
use App\Models\User;

class TestTaskSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::where('email', 'test@example.com')->first();
        if (!$user) return;

        Task::factory()
            ->count(10)
            ->for($user)
            ->create();

        $user = User::where('email', 'ivan.petrovich@example.com')->first();
        if (!$user) return;

        Task::factory()
            ->count(5)
            ->for($user)
            ->create();
    }
}
