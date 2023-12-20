<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class AnimalUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string'],
            'size' => ['required', 'in:Mini,Pequeno,Médio,Grande'],
            'specie_id' => ['required', 'integer', 'exists:species,id'],
            'created_by' => ['required'],
        ];
    }
}
