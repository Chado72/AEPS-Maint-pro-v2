<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCommuneRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $communeId = $this->route('commune')->id ?? $this->route('commune');
        
        return [
            'nom' => 'required|string|max:100|unique:communes,nom,' . $communeId,
            'code' => 'nullable|string|max:20|unique:communes,code,' . $communeId,
        ];
    }

    public function messages()
    {
        return [
            'nom.required' => 'Le nom de la commune est obligatoire.',
            'nom.unique' => 'Cette commune existe déjà.',
        ];
    }
}
