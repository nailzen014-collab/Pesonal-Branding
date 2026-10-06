<?php

namespace Database\Factories;

use App\Models\Skill;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Skill>
 */
class SkillFactory extends Factory
{
    protected $model = Skill::class;

    public function definition(): array
    {
        return [
            'name' => ucfirst(fake()->unique()->word()),
            'category' => fake()->randomElement(array_keys(Skill::CATEGORIES)),
            'level' => fake()->numberBetween(50, 100),
            'icon' => null,
            'sort_order' => fake()->numberBetween(1, 20),
        ];
    }
}
