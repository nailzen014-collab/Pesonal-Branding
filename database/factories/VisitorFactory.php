<?php

namespace Database\Factories;

use App\Models\Visitor;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Visitor>
 */
class VisitorFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'google_id' => (string) Str::uuid(),
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'avatar' => fake()->imageUrl(128, 128, 'people'),
        ];
    }
}
