<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\Task;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Task>
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
        return [
            'project_id' => Project::factory(),
            'team_id' => fn (array $attributes): int => Project::query()->whereKey($attributes['project_id'])->firstOrFail()->team_id,
            'title' => fake()->sentence(4),
            'status' => fake()->randomElement(array_keys(Task::STATUSES)),
            'priority' => fake()->randomElement(array_keys(Task::PRIORITIES)),
            'due_on' => fake()->dateTimeBetween('-1 week', '+1 month'),
        ];
    }
}
