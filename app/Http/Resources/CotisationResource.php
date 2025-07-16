<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CotisationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'membre_id' => $this->membre_id,
            'periode_id' => $this->periode_id,
            'montant' => $this->montant,
            'devise' => $this->devise,
            'date_paiement' => $this->date_paiement,
            'statut' => $this->statut,
            'mode_paiement' => $this->mode_paiement,
            'reference_paiement' => $this->reference_paiement,
        ];
    }
}
