<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSiteRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'nom' => 'required|string|max:150',
            'commune_id' => 'required|exists:communes,id',
            'village_id' => 'required|exists:villages,id',
            'type' => 'required|in:AEPS,PEA',
            'statut' => 'required|in:actif,en_panne,en_maintenance,abandonne',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'date_mise_en_service' => 'nullable|date',
            'observations' => 'nullable|string',
        ];
    }

    public function messages()
    {
        return [
            'nom.required' => 'Le nom du site est obligatoire.',
            'commune_id.required' => 'La commune est obligatoire.',
            'commune_id.exists' => 'La commune sélectionnée n\'existe pas.',
            'village_id.required' => 'Le village est obligatoire.',
            'village_id.exists' => 'Le village sélectionné n\'existe pas.',
            'type.required' => 'Le type de site est obligatoire.',
            'type.in' => 'Le type doit être AEPS ou PEA.',
            'statut.required' => 'Le statut est obligatoire.',
            'statut.in' => 'Le statut n\'est pas valide.',
        ];
    }
}
