<?php

namespace App\Http\Resources;

use App\Http\Resources\nameMembreResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CreditResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'membre_id' => $this->membre_id,
            'montant_demande' => $this->montant_demande,
            'montant_accorde' => $this->montant_accorde,
            'taux_interet' => $this->taux_interet,
            'duree_mois' => $this->duree_mois,
            'montant_total_rembourser' => $this->montant_total_rembourser,
            'montant_mensualite' => $this->montant_mensualite,
            'date_demande' => $this->date_demande,
            'date_approbation' => $this->date_approbation,
            'statut' => $this->statut,
            'motif' => $this->motif,
            'commentaire'=> $this->commentaire,
            // 'membre'=> $this->membre,
            'membre'=> new nameMembreResource($this->membre),
            // 'created_by'=> new UserResource($this->whenLoaded('created_by')),
            // 'approved_by'=> new UserResource($this->whenLoaded('approved_by')),
            // 'rejected_by'=> new UserResource($this->whenLoaded('rejected_by')),
        ];
    }
}
