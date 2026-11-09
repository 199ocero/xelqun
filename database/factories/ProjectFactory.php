<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\Team;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Project>
 */
class ProjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'team_id' => fn (): int => Team::query()->create(['name' => fake()->company(), 'slug' => fake()->unique()->slug(2)])->id,
            'name' => fake()->words(3, true),
            'client' => fake()->company(),
            'status' => fake()->randomElement(array_keys(Project::STATUSES)),
            'budget' => fake()->numberBetween(20, 400) * 100,
            'due_on' => fake()->dateTimeBetween('-1 month', '+3 months'),
        ];
    }
}
