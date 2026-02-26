<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ConfigurationUpdateRequest extends FormRequest
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
            'cle' => ['required', 'string', 'max:100', 'unique:configurations,cle,' . $this->route('configuration')->id],
            'valeur' => ['required', 'string'],
            'description' => ['required', 'string'],
        ];
    }
}
