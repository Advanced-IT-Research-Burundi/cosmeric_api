<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TypeAssistanceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nom' => $this->nom,
            'montant_standard' => $this->montant_standard,
            'conditions' => $this->conditions,
            'documents_requis' => $this->documents_requis,
        ];
    }
}
