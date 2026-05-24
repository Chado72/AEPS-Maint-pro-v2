<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCommuneRequest extends FormRequest
{
    public function authorize()
    {
        return true; // À adapter selon les rôles
    }

    public function rules()
    {
        return [
            'nom' => 'required|string|max:100|unique:communes,nom',
            'code' => 'nullable|string|max:20|unique:communes,code',
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
