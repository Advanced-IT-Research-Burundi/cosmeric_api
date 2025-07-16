<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TransactionStoreRequest extends FormRequest
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
            'type_transaction' => ['required', 'in:cotisation,credit,remboursement,assistance'],
            'reference_transaction' => ['required', 'integer'],
            'montant' => ['required', 'numeric', 'between:-99999999.99,99999999.99'],
            'devise' => ['required', 'in:FBU,USD'],
            'sens' => ['required', 'in:entree,sortie'],
            'date_transaction' => ['required', 'date'],
            'description' => ['required', 'string'],
            'created_at' => ['required'],
        ];
    }
}
