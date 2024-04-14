<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class AnimalResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'size' => $this->size,
            'specie_id' => $this->specie_id,
            'created_by' => $this->created_by,
            'images' => $this->images->map(function ($image) {
                return Storage::disk($image->disk)->url($image->path);
            })
        ];
    }
}
