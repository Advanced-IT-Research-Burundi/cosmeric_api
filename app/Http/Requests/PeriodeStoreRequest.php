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

        if($this->type == 'mensuel'){
            return [
                'type' => ['required', 'in:mensuel,semestriel'],
                'mois' => ['nullable', 'integer'],
                'annee' => ['required', 'integer'],
                'statut' => ['required', 'in:ouvert,ferme'],
                'date_debut' => ['date','before_or_equal:date_fin'],
                'date_fin' => ['date','after_or_equal:date_debut'],
            ];
        }
        return [
            'type' => ['required', 'in:mensuel,semestriel'],
            'mois' => ['nullable', 'integer'],
            'semestre' => ['nullable', 'integer'],
            'annee' => ['required', 'integer'],
            'statut' => ['required', 'in:ouvert,ferme'],
            'date_debut' => ['date','before_or_equal:date_fin'],
            'date_fin' => ['date','after_or_equal:date_debut'],
        ];
    }
}
      