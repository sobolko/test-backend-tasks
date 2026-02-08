<?php

namespace Database\Factories;

use App\Models\Task;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskFactory extends Factory
{
    protected $model = Task::class;

    public function definition(): array
    {
        $statuses = ['new', 'in_progress', 'completed'];
        return [
            'title' => $this->faker->sentence(3),
            'status' => $this->faker->randomElement($statuses),
            'user_id' => null, // назначать вручную в сидере
        ];
    }
}
