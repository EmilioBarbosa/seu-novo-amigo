<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class AnimalStoreRequest extends FormRequest
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
            'images' => ['required', 'array', 'min:1', 'max:5'],
            'images.*' => ['required', 'image', 'file'],
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'O campo nome é obrigatório.',
            'size.required' => 'O campo tamanho é obrigatório.',
            'size.in' => 'O tamanho selecionado não é válido.',
            'specie_id.required' => 'O campo espécie é obrigatório.',
            'specie_id.exists' => 'A espécie selecionada não é válida.',
            'images.required' => 'É necessário enviar pelo menos uma imagem.',
            'images.array' => 'As imagens devem ser fornecidas em um formato de array.',
            'images.min' => 'É necessário enviar pelo menos uma imagem.',
            'images.*.required' => 'Cada imagem deve ser um arquivo de imagem válido.',
            'images.*.image' => 'Cada arquivo enviado deve ser uma imagem válida.',
        ];
    }

    public function prepareForValidation()
    {
        return $this->merge([
           'created_by' => auth()->id(),
        ]);
    }

}
