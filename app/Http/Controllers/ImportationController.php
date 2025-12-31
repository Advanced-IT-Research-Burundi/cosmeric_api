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
     
        foreach ($request->cotisations as $cotisation) {
            // Check if  is cotisation or rembouressement

            if($cotisation['matricule'] == null){
                continue;
            }
            // if is cotisation or rembouressement create new cotisation mensuelle

            $type = "COTISATION";
            if($cotisation['global'] || $cotisation['restant'] ) {
                $type = "REMBOURSEMENT";
            }
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
                'type' => $type,
            ]);
            
        }

        return sendResponse([], 'Cotisation created successfully', 201);


    }
}
