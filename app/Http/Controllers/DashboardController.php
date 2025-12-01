<?php

namespace App\Http\Controllers;

use App\Models\Assistance;
use App\Models\Cotisation;
use App\Models\Credit;
use App\Models\Membre;
use App\Models\Remboursement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    //
    public function index()
    {
        // NOTE: this implementation assumes monetary columns are named 'montant'.
        // If your schema uses another column name (e.g. 'amount'), replace 'montant' accordingly.

        // Basic stats
        $cotisationsCeMois = Cotisation::whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->sum('montant');

        $cotisationsCumulees = Cotisation::sum('montant');

        $membresActifs = Membre::where('statut', 'actif')->count();
        $membresInactifs = Membre::where('statut', 'inactif')->count();

        $creditsTotal = Credit::sum('montant_total_rembourser');
        $creditsEnCours = Credit::where('statut', 'en cours')->count();

        $dettesEnAttente = Remboursement::where('statut', 'en attente')->sum('montant_paye');

        $demandesAssistance = Assistance::count();
        $aidesAccordees = Assistance::where('statut', 'accordee')->count();

        $totalRembourse = Remboursement::where('statut', 'rembourse')->sum('montant_paye');
        $tauxRecouvrement = 0;
        $baseRecouvrement = $totalRembourse + $dettesEnAttente;
        if ($baseRecouvrement > 0) {
            $tauxRecouvrement = round(($totalRembourse / $baseRecouvrement) * 100, 2);
        }

        // Solde disponible (approx): total cotisations - (total credits + total assistances)
        $totalAssistances = Assistance::sum('montant');
        $soldeDisponible = $cotisationsCumulees - ($creditsTotal + $totalAssistances);

        // Charts data
        // last 12 months for cotisations & tendance
        $labels12 = [];
        $cotisationsValues = [];
        $creditsValuesForTrend = [];
        $assistancesValuesForTrend = [];

        for ($i = 11; $i >= 0; $i--) {
            $dt = Carbon::now()->subMonths($i);
            $labels12[] = $dt->translatedFormat('M Y'); // ex: "déc. 2025" (requires locales)
            $cotisationsValues[] = (float) Cotisation::whereYear('created_at', $dt->year)
                ->whereMonth('created_at', $dt->month)
                ->sum('montant');

            $creditsValuesForTrend[] = (float) Credit::whereYear('created_at', $dt->year)
                ->whereMonth('created_at', $dt->month)
                ->sum('montant_total_rembourser');

            $assistancesValuesForTrend[] = (float) Assistance::whereYear('created_at', $dt->year)
                ->whereMonth('created_at', $dt->month)
                ->sum('montant');
        }

        // credits vs remboursements (last 6 months)
        $labels6 = [];
        $creditsAccordes6 = [];
        $rembourses6 = [];
        for ($i = 5; $i >= 0; $i--) {
            $dt = Carbon::now()->subMonths($i);
            $labels6[] = $dt->translatedFormat('M Y');
            $creditsAccordes6[] = (float) Credit::whereYear('created_at', $dt->year)
                ->whereMonth('created_at', $dt->month)
                ->sum('montant_total_rembourser');

            $rembourses6[] = (float) Remboursement::whereYear('created_at', $dt->year)
                ->whereMonth('created_at', $dt->month)
                ->sum('montant_paye');
        }

        // membres repartition
        $membresActifsCount = $membresActifs;
        $membresInactifsCount = $membresInactifs;

        // assistances par type
        $assistancesByType = Assistance::select('type_assistance_id', DB::raw('COUNT(*) as total'))
            ->groupBy('type_assistance_id')
            ->join('type_assistances', 'assistances.type_assistance_id', '=', 'type_assistances.id')
            ->select('type_assistances.nom as type', DB::raw('COUNT(assistances.id) as total'))
            ->groupBy('type_assistances.nom')
            ->orderByDesc('total')
            ->get();

        $assistLabels = $assistancesByType->pluck('type')->toArray();
        $assistValues = $assistancesByType->pluck('total')->toArray();

        $response = [
            'success' => true,
            'stats' => [
                'cotisationsMois' => (float) $cotisationsCeMois,
                'cotisationsCumulatif' => (float) $cotisationsCumulees,
                'evolutionCotisations' => 0, // leave 0 or compute w/ previous month data if desired
                'membresActifs' => $membresActifsCount,
                'membresInactifs' => $membresInactifsCount,
                'creditsTotal' => (float) $creditsTotal,
                'creditsEnCours' => $creditsEnCours,
                'dettesAttente' => (float) $dettesEnAttente,
                'demandesAssistance' => $demandesAssistance,
                'aidesAccordees' => $aidesAccordees,
                'tauxRecouvrement' => $tauxRecouvrement,
                'soldeDisponible' => (float) $soldeDisponible,
            ],
            'charts' => [
                'cotisations' => [
                    'labels' => $labels12,
                    'values' => $cotisationsValues,
                ],
                'membres' => [
                    'actifs' => $membresActifsCount,
                    'inactifs' => $membresInactifsCount,
                ],
                'credits' => [
                    'labels' => $labels6,
                    'accordes' => $creditsAccordes6,
                    'rembourses' => $rembourses6,
                ],
                'assistances' => [
                    'labels' => $assistLabels,
                    'values' => $assistValues,
                ],
                'tendance' => [
                    'labels' => $labels12,
                    'cotisations' => $cotisationsValues,
                    'credits' => $creditsValuesForTrend,
                    'assistances' => $assistancesValuesForTrend,
                ],
            ],
        ];

        return response()->json($response);
    }
}
