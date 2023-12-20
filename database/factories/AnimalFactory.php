<?php

namespace Database\Factories;

use App\Enums\AnimalSize;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Animal;
use App\Models\Specie;
use App\Models\User;

class AnimalFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Animal::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'size' => $this->faker->randomElement(AnimalSize::cases())->value,
            'specie_id' => Specie::factory(),
            'created_by' => User::factory(),
        ];
    }
}
