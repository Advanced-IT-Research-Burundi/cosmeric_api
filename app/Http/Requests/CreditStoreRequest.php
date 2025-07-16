<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreditStoreRequest extends FormRequest
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
            'montant_demande' => ['required', 'numeric', 'between:-99999999.99,99999999.99'],
            'montant_accorde' => ['required', 'numeric', 'between:-99999999.99,99999999.99'],
            'taux_interet' => ['required', 'numeric', 'between:-999.99,999.99'],
            'duree_mois' => ['required', 'integer'],
            'montant_total_rembourser' => ['required', 'numeric', 'between:-99999999.99,99999999.99'],
            'montant_mensualite' => ['required', 'numeric', 'between:-99999999.99,99999999.99'],
            'date_demande' => ['required', 'date'],
            'date_approbation' => ['required', 'date'],
            'statut' => ['required', 'in:en_attente,approuve,rejete,en_cours,termine'],
            'motif' => ['required', 'string'],
        ];
    }
}
