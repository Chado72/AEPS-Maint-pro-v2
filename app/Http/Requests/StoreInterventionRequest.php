<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreInterventionRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'site_id' => 'required|exists:sites,id',
            'forage_id' => 'nullable|exists:forages,id',
            'type_intervention' => 'required|in:preventive,corrective,urgence,installation,rehabilitation',
            'statut' => 'required|in:planifiee,en_cours,terminee,annulee',
            'date_intervention' => 'required|date',
            'description' => 'required|string',
            'cout' => 'nullable|numeric|min:0',
            'duree_heures' => 'nullable|numeric|min:0',
            'pieces_utilisees' => 'nullable|array',
            'pieces_utilisees.*.spare_part_id' => 'required_with:pieces_utilisees|exists:spare_parts,id',
            'pieces_utilisees.*.quantite' => 'required_with:pieces_utilisees|integer|min:1',
        ];
    }

    public function messages()
    {
        return [
            'site_id.required' => 'Le site est obligatoire.',
            'site_id.exists' => 'Le site sélectionné n\'existe pas.',
            'forage_id.exists' => 'Le forage sélectionné n\'existe pas.',
            'type_intervention.required' => 'Le type d\'intervention est obligatoire.',
            'type_intervention.in' => 'Le type d\'intervention n\'est pas valide.',
            'statut.required' => 'Le statut est obligatoire.',
            'statut.in' => 'Le statut n\'est pas valide.',
            'date_intervention.required' => 'La date d\'intervention est obligatoire.',
            'date_intervention.date' => 'La date n\'est pas valide.',
            'description.required' => 'La description est obligatoire.',
            'cout.min' => 'Le coût ne peut pas être négatif.',
            'duree_heures.min' => 'La durée ne peut pas être négative.',
        ];
    }
}
