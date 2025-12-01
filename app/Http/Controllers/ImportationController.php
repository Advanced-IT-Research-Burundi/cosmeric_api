<?php

namespace App\Http\Controllers;

use App\Models\CotisationMensuelle;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ImportationController extends Controller
{
    //
    public function cotisation(Request $request){

        CotisationMensuelle::create($request->all());
        $date = Carbon::parse($request->date_cotisation)->format('Y-m');

        // check if date already exists
        $existingCotisation = CotisationMensuelle::where('date_cotisation', $date)->first();
        if ($existingCotisation) {
            return sendError('Cotisation for this date already exists', [], 409);
        }
        // checko if cotisations array is present
        // if (!isset($request->cotisations) || !is_array($request->cotisations)) {
        //     return sendError('Invalid cotisations data', [], 400);
        // }

        // check if cotisations array has a valid format
        // foreach ($request->cotisations as $cotisation) {
        //     if (!isset($cotisation['name'], $cotisation['matricule'], $cotisation['nomero_dossier'], $cotisation['global'], $cotisation['regle'], $cotisation['restant'], $cotisation['retenu'])) {
        //         return sendError('Invalid cotisation data', [], 400);
        //     }
        // }
        
        foreach ($request->cotisations as $cotisation) {
            CotisationMensuelle::create([
                'name' => $cotisation['name'],
                'matricule' => $cotisation['matricule'],
                'nomero_dossier' => $cotisation['nomero_dossier'],
                'global' => $cotisation['global'],
                'regle' => $cotisation['regle'],
                'restant' => $cotisation['restant'],
                'retenu' => $cotisation['retenu'],
                'date_cotisation' => $date,
                'user_id' => auth()->id(),
            ]);
            
        }

        return sendResponse([], 'Cotisation created successfully', 201);


    }
}
