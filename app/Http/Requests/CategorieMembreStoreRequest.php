<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CategorieMembreStoreRequest extends FormRequest
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
            'nom' => ['required', 'string', 'max:100'],
            'montant_cotisation' => ['required', 'numeric', 'between:-99999999.99,99999999.99'],
            'devise' => ['required', 'in:FBU,USD'],
            'frequence_paiement' => ['required', 'in:mensuel,semestriel'],
            'description' => ['required', 'string'],
        ];
    }
}
