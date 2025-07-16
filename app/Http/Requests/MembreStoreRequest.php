<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MembreStoreRequest extends FormRequest
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
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'matricule' => ['required', 'string', 'max:50', 'unique:membres,matricule'],
            'nom' => ['required', 'string', 'max:100'],
            'prenom' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150'],
            'telephone' => ['required', 'string', 'max:20'],
            'categorie_id' => ['required', 'integer', 'exists:categories,id'],
            'statut' => ['required', 'in:actif,inactif,suspendu'],
            'date_adhesion' => ['required', 'date'],
        ];
    }
}
