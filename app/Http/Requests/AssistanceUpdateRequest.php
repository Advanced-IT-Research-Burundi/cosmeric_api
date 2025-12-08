<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AssistanceUpdateRequest extends FormRequest
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
            // 'membre_id' => ['required', 'integer', 'exists:membres,id'],
            'type_assistance_id' => ['required', 'integer', 'exists:type_assistances,id'],
            'montant' => ['required', 'numeric', 'between:-99999999.99,99999999.99'],
            'date_demande' => ['required', 'date'],
            'date_approbation' => ['required', 'date'],
            'date_versement' => ['required', 'date'],
            'statut' => ['required', 'in:en_attente,approuve,rejete,verse'],
            'justificatif' => ['required', 'string', 'max:255'],
            'motif_rejet' => ['required', 'string'],
        ];
    }
}
