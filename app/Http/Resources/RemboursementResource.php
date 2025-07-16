<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RemboursementResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'credit_id' => $this->credit_id,
            'numero_echeance' => $this->numero_echeance,
            'montant_prevu' => $this->montant_prevu,
            'montant_paye' => $this->montant_paye,
            'date_echeance' => $this->date_echeance,
            'date_paiement' => $this->date_paiement,
            'statut' => $this->statut,
            'penalite' => $this->penalite,
        ];
    }
}
