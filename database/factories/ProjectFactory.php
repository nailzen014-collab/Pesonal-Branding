<?php

namespace Database\Factories;

use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Project>
 */
class ProjectFactory extends Factory
{
    protected $model = Project::class;

    public function definition(): array
    {
        $title = ucfirst(fake()->unique()->words(3, true));

        return [
            'title' => $title,
            'slug' => Str::slug($title),
            'summary' => fake()->sentence(12),
            'description' => fake()->paragraphs(3, true),
            'category' => fake()->randomElement(['Laravel', 'PHP', 'Blade', 'JavaScript']),
            'repo_url' => 'https://github.com/nailzen014-collab/'.Str::slug($title),
            'demo_url' => null,
            'thumbnail' => null,
            'source' => 'manual',
            'github_id' => null,
            'is_featured' => false,
            'is_published' => true,
            'sort_order' => fake()->numberBetween(1, 20),
        ];
    }

    public function featured(): static
    {
        return $this->state(fn () => ['is_featured' => true]);
    }

    public function draft(): static
    {
        return $this->state(fn () => ['is_published' => false]);
    }
}
