<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RapportStoreRequest extends FormRequest
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
            'titre' => ['required', 'string', 'max:200'],
            'type_rapport' => ['required', 'in:mensuel,semestriel,annuel,personnalise'],
            'periode_debut' => ['required', 'date'],
            'periode_fin' => ['required', 'date'],
            'genere_par' => ['required'],
            'fichier_path' => ['required', 'string', 'max:255'],
            'statut' => ['required', 'in:genere,envoye,archive'],
            'created_at' => ['required'],
        ];
    }
}
