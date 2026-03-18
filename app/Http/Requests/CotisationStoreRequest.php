<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CotisationStoreRequest extends FormRequest
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
            'montant' => ['required', 'numeric', 'between:-99999999.99,99999999.99'],
            'devise' => ['required', 'in:FBU,USD'],
            'date_paiement' => ['required', 'date'],
            'statut' => ['required', 'in:paye,en_attente,en_retard'],
            'mode_paiement' => ['required', 'string', 'max:50'],
            'reference_paiement' => ['required', 'string', 'max:100'],
        ];
    }
}
