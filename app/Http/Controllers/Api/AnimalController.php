<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\AnimalStoreRequest;
use App\Http\Requests\Api\AnimalUpdateRequest;
use App\Http\Resources\Api\AnimalCollection;
use App\Http\Resources\Api\AnimalResource;
use App\Models\Animal;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

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

    public function store(AnimalStoreRequest $request): AnimalResource
    {
        $animal = Animal::create($request->validated());

        return new AnimalResource($animal);
    }

    public function show(Request $request, Animal $animal): AnimalResource
    {
        return new AnimalResource($animal);
    }

    public function update(AnimalUpdateRequest $request, Animal $animal): AnimalResource|\Illuminate\Http\JsonResponse
    {
        if(auth()->id() !== $animal->created_by) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $animal->update($request->validated());

        return new AnimalResource($animal);
    }

    public function destroy(Request $request, Animal $animal): Response|\Illuminate\Http\JsonResponse
    {
        if(auth()->id() !== $animal->created_by) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $animal->delete();

        return response()->noContent();
    }
}
