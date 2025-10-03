<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PeriodeStoreRequest extends FormRequest
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
            'type' => ['required', 'in:mensuel,semestre'],
            'mois' => ['nullable', 'integer'],
            'semestre' => ['nullable', 'integer'],
            'annee' => ['required', 'integer'],
            'statut' => ['required', 'in:ouvert,ferme'],
            'date_debut' => ['required', 'date'],
            'date_fin' => ['required', 'date'],
        ];
    }
}
