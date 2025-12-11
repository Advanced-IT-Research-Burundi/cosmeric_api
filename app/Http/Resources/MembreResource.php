<?php

namespace App\Http\Resources;

use App\Models\Assistance;
use App\Models\Cotisation;
use App\Models\Credit;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MembreResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        // Get credit data
        $creditsEnCours = Credit::where("membre_id", $this->id)
            ->where("statut", "en_cours")
            ->get();

        $creditTotal = $creditsEnCours->sum("montant_total_rembourser");
        $creditReste = $creditsEnCours->sum("montant_restant");

        // Get assistances count
        $assistancesSum = Assistance::where("membre_id", $this->id)->sum("montant");

        return [
            'id' => $this->id,

            // User info
            'user_id'   => $this->user?->id,
            'nom'       => $this->user?->nom,
            'prenom'    => $this->user?->prenom,

            // Contact
            'matricule' => $this->matricule,
            'email'     => $this->email,
            'telephone' => $this->telephone,

            // Category
            'categorie_id'           => $this->categorie_id,
            'categorie_nom'          => $this->categorie?->nom,
            'categorie_cotisation'   => $this->categorie?->montant_cotisation,
            'categorie_devise'       => $this->categorie?->devise,

            // Use the member's category currency as default extraction currency
            'devise' => $this->categorie?->devise ?? "FBU",

            // Status
            'statut'        => $this->statut,
            'date_adhesion' => $this->date_adhesion,

            // Cotisations
            'cotisations_paid'       => Cotisation::where("membre_id", $this->id)->where("statut", "paye")->sum('montant'),
            'cotisations_manquantes' => $this->cotisations_manquantes ?? 0,  // optional field

            // Credits
            'credit_encours' => $creditTotal,
            'credit_restant' => $creditReste,
            'credits'        => CreditResource::collection($this->credits),

            // Assistances
            'montant_recues_en_assistances' => $assistancesSum,
            'assistances'        => AssistanceResource::collection($this->assistances),
            'cotisation'        => CotisationResource::collection($this->cotisations),

            'retard' => Cotisation::where('statut', 'en_retard')->count()
        ];
    }
}
