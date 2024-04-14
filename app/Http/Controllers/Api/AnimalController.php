<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\AnimalStoreRequest;
use App\Http\Requests\Api\AnimalUpdateRequest;
use App\Http\Resources\Api\AnimalCollection;
use App\Http\Resources\Api\AnimalResource;
use App\Models\Animal;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

class AnimalController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['index', 'show']);
    }

    public function index(Request $request): AnimalCollection
    {
        $animals = Animal::all();

        return new AnimalCollection($animals);
    }

    public function store(AnimalStoreRequest $request): AnimalResource | JsonResponse
    {
        try {
            $validated = $request->validated();

            $validated['created_by'] = auth()->id();

            $animal = Animal::create($validated);

            foreach ($request->images as $image) {
                $path = $image->store("animals/$animal->id");

                $animal->images()->create([
                    'path' => $path,
                    'disk' => config('app.filesystem_disk')
                ]);
            }

            return new AnimalResource($animal);

        } catch (\Exception $exception) {
            return response()->json(['message' => $exception->getMessage()], 500);
        }
    }

    public function show(Request $request, Animal $animal): AnimalResource
    {
        return new AnimalResource($animal);
    }

    public function update(AnimalUpdateRequest $request, Animal $animal): AnimalResource|JsonResponse
    {
        try {
            $animal->update($request->validated());

            if ($request->has('images')) {
                $paths_to_delete = $animal->images->pluck('path');

                Storage::delete($paths_to_delete->toArray());

                $animal->images()->delete();

                foreach ($request->images as $image) {
                    $path = $image->store("animals/$animal->id");

                    $animal->images()->create([
                        'path' => $path,
                        'disk' => config('app.filesystem_disk')
                    ]);
                }
            }

            return new AnimalResource($animal);
        } catch (\Exception $exception) {
            return response()->json(['message' => $exception->getMessage()], 500);
        }

    }

    public function destroy(Request $request, Animal $animal): Response|JsonResponse
    {
        if(auth()->id() !== $animal->created_by) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $animal->delete();

        return response()->noContent();
    }
}
