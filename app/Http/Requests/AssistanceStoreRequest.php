<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AssistanceStoreRequest extends FormRequest
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
            'membre_id' => ['required', 'integer', 'exists:membres,id'],
            'type_assistance_id' => ['required', 'integer', 'exists:type_assistances,id'],
            'montant' => ['required', 'numeric'],
            'date_demande' => ['required', 'date'],
            'date_approbation' => ['nullable', 'date'],
            'date_versement' => ['nullable', 'date'],
            'statut' => ['required', 'in:en_attente,approuve,rejete,verse'],
            'justificatif' => ['nullable'], // Handle manually or as file
            'motif_rejet' => ['nullable', 'string'],
        ];
    }
}
