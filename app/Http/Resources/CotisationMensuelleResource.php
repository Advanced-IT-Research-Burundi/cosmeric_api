<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CotisationMensuelleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'matricule' => $this->matricule,
            'nomero_dossier' => $this->nomero_dossier,
            'global' => $this->global,
            'regle' => $this->regle,
            'restant' => $this->restant,
            'retenu' => $this->retenu,
            'date_cotisation' => $this->date_cotisation,
            'user_id' => $this->user_id,
        ];
    }
}
