<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'membre_id' => $this->membre_id,
            'type_transaction' => $this->type_transaction,
            'reference_transaction' => $this->reference_transaction,
            'montant' => $this->montant,
            'devise' => $this->devise,
            'sens' => $this->sens,
            'date_transaction' => $this->date_transaction,
            'description' => $this->description,
            'created_at' => $this->created_at,
        ];
    }
}
