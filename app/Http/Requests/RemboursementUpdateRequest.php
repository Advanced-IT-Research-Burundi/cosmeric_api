<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RemboursementUpdateRequest extends FormRequest
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
            'credit_id' => ['required', 'integer', 'exists:credits,id'],
            'numero_echeance' => ['required', 'integer'],
            'montant_prevu' => ['required', 'numeric', 'between:-99999999.99,99999999.99'],
            'montant_paye' => ['required', 'numeric', 'between:-99999999.99,99999999.99'],
            'date_echeance' => ['required', 'date'],
            'date_paiement' => ['required', 'date'],
            'statut' => ['required', 'in:prevu,paye,en_retard'],
            'penalite' => ['required', 'numeric', 'between:-99999999.99,99999999.99'],
        ];
    }
}
