<?php

namespace App\Http\Controllers;

use App\Models\Assistance;
use App\Models\Cotisation;
use App\Models\Credit;
use App\Models\Membre;
use App\Models\Remboursement;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    //
    public function index()
    {
        return response()->json([
            'success' => true,
            'data' => [
                //                 Nshak mur dashboard haze kw afficha ibi :
                // 1. Cotisation de ce mois
                // 2. ⁠cotisation cumulées(total guhera au début)
                // 3. ⁠membres actifs/inactifs
                // 4. ⁠crédits accordés
                // 5. ⁠dettes en attente
                // 6. Crédit en cours
                // 6. ⁠demandes assistance
                // 7. ⁠taux recouvrement
                // 8. ⁠solde disponible
                'cotisation_ce_mois' => Cotisation::whereMonth('created_at', now()->month)->count(),
                'cotisation_cumulees' => Cotisation::count(),
                'membres_actifs' => Membre::where('statut', 'actif')->count(),
                'membres_inactifs' => Membre::where('statut', 'inactif')->count(),
                'credits_accordes' => Credit::count(),
                'dettes_en_attente' => Remboursement::where('statut', 'en attente')->count(),
                'credits_en_cours' => Credit::where('statut', 'en cours')->count(),
                'demandes_assistance' => Assistance::count(),
                'taux_recouvrement' => 0,
                'solde_disponible' => 0,
            ],
        ]);
    }
}
