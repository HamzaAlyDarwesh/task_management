<?php

namespace Database\Factories;

use App\Models\TaskStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $taskStatus = TaskStatus::firstOrCreate([
            'name' => 'pending'
        ]);
        return [
            'title' => $this->faker->sentence,
            'description' => $this->faker->paragraph,
            'status_id' => $taskStatus->id,
            'user_id' => User::factory()->create()->id,
        ];
    }
}
