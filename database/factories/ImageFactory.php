<?php

namespace Database\Factories;

use App\Models\Animal;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Str;
use App\Models\Image;

class ImageFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Image::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        $imageable = $this->imageable();

        return [
            'path' => $this->faker->word() . "/" . $this->faker->word() . ".jpg",
            'disk' => config('app.filesystem_disk'),
            'imageable_id' => $imageable::factory(),
            'imageable_type' => $imageable,
        ];
    }


    public function imageable()
    {
        return $this->faker->randomElement([
            User::class,
            Animal::class
        ]);
    }
}
