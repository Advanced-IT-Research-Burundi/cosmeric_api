<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RapportResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'titre' => $this->titre,
            'type_rapport' => $this->type_rapport,
            'periode_debut' => $this->periode_debut,
            'periode_fin' => $this->periode_fin,
            'genere_par' => $this->genere_par,
            'fichier_path' => $this->fichier_path,
            'statut' => $this->statut,
            'created_at' => $this->created_at,
        ];
    }
}
