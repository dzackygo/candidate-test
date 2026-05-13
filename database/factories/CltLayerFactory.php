<?php

namespace Database\Factories;

use App\Models\CltLayup;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CltLayer>
 */
class CltLayerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'layup_id' => CltLayup::factory(),
            'layer_order' => fake()->unique()->numberBetween(1, 12),
            'thickness' => fake()->randomFloat(3, 10, 80),
            'width' => fake()->randomFloat(3, 600, 2400),
            'angle' => fake()->randomElement([0, 45, 90]),
        ];
    }
}
