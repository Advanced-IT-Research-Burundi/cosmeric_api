<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CotisationMensuelleUpdateRequest extends FormRequest
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
            'name' => ['nullable', 'string'],
            'matricule' => ['nullable', 'string'],
            'nomero_dossier' => ['nullable', 'string'],
            'global' => ['nullable', 'string'],
            'regle' => ['nullable', 'string'],
            'restant' => ['nullable', 'string'],
            'retenu' => ['nullable', 'string'],
            'date_cotisation' => ['nullable', 'string'],
            'user_id' => ['nullable', 'integer'],
        ];
    }
}
