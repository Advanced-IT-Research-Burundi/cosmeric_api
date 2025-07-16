<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AssistanceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'membre_id' => $this->membre_id,
            'type_assistance_id' => $this->type_assistance_id,
            'montant' => $this->montant,
            'date_demande' => $this->date_demande,
            'date_approbation' => $this->date_approbation,
            'date_versement' => $this->date_versement,
            'statut' => $this->statut,
            'justificatif' => $this->justificatif,
            'motif_rejet' => $this->motif_rejet,
        ];
    }
}
