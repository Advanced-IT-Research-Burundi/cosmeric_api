<?php

namespace App\Http\Resources;

use App\Models\Assistance;
use App\Models\Cotisation;
use App\Models\Credit;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MembreResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user?->id,
            "nom" => $this->user?->nom,
            "prenom" => $this->user?->prenom,
            'matricule' => $this->matricule,
            'email' => $this->email,
            'telephone' => $this->telephone,
            'categorie_id' => $this->categorie_id,
            'statut' => $this->statut,
            'date_adhesion' => $this->date_adhesion,
            "categorie_nom" => $this->categorie?->nom,
            "categorie_cotisation" => $this->categorie?->montant_cotisation,
            "categorie_devise" => $this->categorie?->devise,
            "cotisations_paid" => Cotisation::where("membre_id", $this->id)->sum('montant'),
            "cotisations" => CotisationResource::collection($this->cotisations),
            "credits" => CreditResource::collection($this->credits),
            "remboursements_pending" => Credit::where("membre_id", $this->id)->where("statut", "en_cours")->sum('montant_total_rembourser'),
            "remboursements" => RemboursementResource::where("credit_id", $this->id),
            "assistances" => AssistanceResource::collection($this->assistances),
        ];
    }
}
