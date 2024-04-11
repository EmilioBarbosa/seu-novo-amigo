<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Enums\AnimalSize;
use App\Models\Animal;
use App\Models\Specie;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Testing\Fluent\AssertableJson;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\Api\AnimalController
 */
final class AnimalControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_behaves_as_expected(): void
    {
        Animal::factory()->count(3)->create();

        $response = $this->getJson(route('animals.index'));

        $response->assertOk();

        $response->assertJson(fn (AssertableJson $json) =>
            $json->has('data', 3)
        );
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\Api\AnimalController::class,
            'store',
            \App\Http\Requests\Api\AnimalStoreRequest::class
        );
    }

    #[Test]
    public function store_saves(): void
    {
        Storage::fake(config('app.filesystem_disk'));

        $name = $this->faker->name();

        $size = $this->faker->randomElement(AnimalSize::cases())->value;

        $specie = Specie::factory()->create();

        $user = User::factory()->create();

        $response =$this->actingAs($user)->postJson(route('animals.store'), [
            'name' => $name,
            'size' => $size,
            'specie_id' => $specie->id,
            'images' => [
                UploadedFile::fake()->image('photo.jpg')
            ]
        ]);

        $animals = Animal::query()
            ->where('name', $name)
            ->where('size', $size)
            ->where('specie_id', $specie->id)
            ->where('created_by', $user->id)
            ->get();

        $this->assertCount(1, $animals);

        $response->assertCreated();

        $response->assertJsonPath('data.name', $name);
    }


    #[Test]
    public function show_behaves_as_expected(): void
    {
        $animal = Animal::factory()->create();

        $response = $this->getJson(route('animals.show', $animal->id));

        $response->assertOk();

        $response->assertJsonPath('data.id', $animal->id);
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\Api\AnimalController::class,
            'update',
            \App\Http\Requests\Api\AnimalUpdateRequest::class
        );
    }

    #[Test]
    public function update_behaves_as_expected(): void
    {
        $animal = Animal::factory()->create();

        $name = $this->faker->name();

        $size = $this->faker->randomElement(AnimalSize::cases())->value;

        $specie = Specie::factory()->create();

        $user = $animal->user;

        $this->actingAs($user);

        $response = $this->putJson(route('animals.update', $animal), [
            'name' => $name,
            'size' => $size,
            'specie_id' => $specie->id,
            'created_by' => $user->id,
        ]);

        $animal->refresh();

        $response->assertOk();

        $response->assertJsonStructure([]);

        $this->assertEquals($name, $animal->name);

        $this->assertEquals($size, $animal->size);

        $this->assertEquals($specie->id, $animal->specie_id);

        $this->assertEquals($user->id, $animal->created_by);
    }

    #[Test]
    public function only_user_that_created_the_animal_can_updated_it(): void
    {
        $name = $this->faker->name();

        $size = $this->faker->randomElement(AnimalSize::cases())->value;

        $specie = Specie::factory()->create();

        $user = User::factory()->has(Animal::factory())->create();

        $animal = $user->animals->first();

        $wrong_user = User::factory()->create();

        $this->actingAs($wrong_user);

        $response = $this->putJson(route('animals.update', $animal->id), [
            'name' => $name,
            'size' => $size,
            'specie_id' => $specie->id,
            'created_by' => $user->id,
        ]);

        $response->assertForbidden();
    }


    #[Test]
    public function can_destroy(): void
    {
        $user = User::factory()
                    ->has(Animal::factory())
                    ->create();

        $animal = $user->animals()->first();

        $this->actingAs($user);

        $response = $this->deleteJson(route('animals.destroy', $animal->id));

        $response->assertNoContent();

        $this->assertModelMissing($animal);
    }

    #[Test]
    public function only_user_who_created_the_animal_can_delete_it(): void
    {
        $user = User::factory()
            ->has(Animal::factory())
            ->create();

        $animal = $user->animals()->first();

        $wrong_user = User::factory()->create();

        $this->actingAs($wrong_user);

        $response = $this->deleteJson(route('animals.destroy', $animal->id));

        $response->assertUnauthorized();

        $this->assertModelExists($animal);
    }
}
