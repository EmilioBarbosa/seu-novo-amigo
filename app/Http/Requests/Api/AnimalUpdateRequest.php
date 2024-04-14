<?php

namespace App\Http\Requests\Api;

use App\Enums\AnimalSize;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AnimalUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->id() === $this->animal->created_by;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => ['sometimes','string'],
            'size' => ['sometimes', Rule::enum(AnimalSize::class)],
            'specie_id' => ['sometimes', 'integer', 'exists:species,id'],
            'images' => ['sometimes', 'array', 'max:5'],
            'images.*' => ['sometimes', 'image', 'file']
        ];
    }

    public function messages()
    {
        return [
            'size.in' => 'O tamanho selecionado não é válido.',
            'specie_id.exists' => 'A espécie selecionada não é válida.',
            'images.required' => 'É necessário enviar pelo menos uma imagem.',
            'images.array' => 'As imagens devem ser fornecidas em um formato de array.',
            'images.max' => 'Só é permitido enviar 5 imagens.',
            'images.*.required' => 'Cada imagem deve ser um arquivo de imagem válido.',
            'images.*.image' => 'Cada arquivo enviado deve ser uma imagem válida.',
        ];
    }
}
