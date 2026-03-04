<?php

namespace App\Http\Controllers;

use App\Models\CotisationMensuelle;
use App\Services\ImportationService;
use App\Jobs\ProcessImportJob;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ImportationController extends Controller
{
    protected $importationService;

    public function __construct(ImportationService $importationService)
    {
        $this->importationService = $importationService;
    }

    public function cotisation(Request $request)
    {
        try {
            return DB::transaction(function () use ($request) {
                $date = Carbon::parse($request->date_cotisation)->format('Y-m');

                // // Check if date already exists
                // $existingCotisation = CotisationMensuelle::where('date_cotisation', $date)->first();
                // if ($existingCotisation) {
                //     throw new \Exception('La cotisation pour cette date existe déjà');
                // }

                // Validate data first
                $validCotisations = [];
                foreach ($request->cotisations as $cotisation) {
                    if (empty($cotisation['matricule']) || empty($cotisation['name']) || !is_numeric($cotisation['matricule'])) {
                        continue;
                    }
                    $validCotisations[] = $cotisation;
                }

                if (empty($validCotisations)) {
                    throw new \Exception('Aucune entrée valide trouvée dans les données d\'importation');
                }

                // Create staging records
                $stagingRecords = [];
                foreach ($validCotisations as $cotisation) {
                    $type = "COTISATION";
                    if (!empty($cotisation['global']) || !empty($cotisation['restant'])) {
                        $type = "REMBOURSEMENT";
                    }

                    $stagingRecord = CotisationMensuelle::create([
                        'name' => $cotisation['name'],
                        'matricule' => $cotisation['matricule'],
                        'nomero_dossier' => $cotisation['nomero_dossier'] ?? null,
                        'global' => floatval($cotisation['global'] ?? 0),
                        'regle' => floatval($cotisation['regle'] ?? 0),
                        'restant' => floatval($cotisation['restant'] ?? 0),
                        'retenu' => floatval($cotisation['retenu'] ?? 0),
                        'date_cotisation' => $date,
                        'user_id' => auth()->id(),
                        'type' => $type,
                    ]);
                    $stagingRecords[] = $stagingRecord->toArray();
                }

                // Dispatch background job to process staging records and avoid HTTP timeout
                ProcessImportJob::dispatch($date, auth()->id() ?? null);

                return sendResponse([], 'Importation lancée en arrière-plan. Vous serez notifié du résultat.', 202);
            });
        } catch (\Exception $e) {
            \Log::error('Erreur lors de l\'importation: ' . $e->getMessage());
            return sendError('Erreur lors de l\'importation: ' . $e->getMessage(), [], 500);
        }
    }
}
