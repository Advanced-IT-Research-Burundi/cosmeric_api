<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategorieMembreResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nom' => $this->nom,
            'montant_cotisation' => $this->montant_cotisation,
            'devise' => $this->devise,
            'frequence_paiement' => $this->frequence_paiement,
            'description' => $this->description,
        ];
    }
}
