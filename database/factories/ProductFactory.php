<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->word,
            'title' => $this->faker->sentence,
            'description' => $this->faker->paragraph,
            'category_ref_id' => $this->faker->numberBetween(1, 10),
            'brand_ref_id' => $this->faker->numberBetween(1, 10),
            'price' => $this->faker->randomFloat(2, 10, 1000),
            'discount_price' => $this->faker->randomFloat(2, 5,500),
            'benefits_content' => $this->faker->paragraph,
            'ingredients_content' => $this->faker->paragraph,
            'howtouse_content' => $this->faker->paragraph,
            'product_size_id' => $this->faker->numberBetween(1, 5),
            'is_active' => $this->faker->boolean(80)
        ];
    }
}
