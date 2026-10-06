<?php

namespace Database\Factories;

use App\Models\Technology;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Technology>
 */
class TechnologyFactory extends Factory
{
    protected $model = Technology::class;

    public function definition(): array
    {
        return [
            'name' => ucfirst(fake()->unique()->word()),
            'icon' => null,
            'sort_order' => fake()->numberBetween(1, 20),
        ];
    }
}
