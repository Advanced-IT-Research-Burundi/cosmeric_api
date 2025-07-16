<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PeriodeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'mois' => $this->mois,
            'annee' => $this->annee,
            'statut' => $this->statut,
            'date_debut' => $this->date_debut,
            'date_fin' => $this->date_fin,
        ];
    }
}
