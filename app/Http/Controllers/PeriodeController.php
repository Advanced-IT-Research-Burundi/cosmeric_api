<?php

namespace App\Http\Controllers;

use App\Http\Requests\PeriodeStoreRequest;
use App\Http\Requests\PeriodeUpdateRequest;
use App\Http\Resources\PeriodeCollection;
use App\Http\Resources\PeriodeResource;
use App\Models\Periode;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PeriodeController extends Controller
{
    public function index(Request $request)
    {
        $query = Periode::query();



        // Search
        if ($request->has('search') && $request->search) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('type', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('mois', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('semestre', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('annee', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('statut', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('date_debut', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('date_fin', 'LIKE', "%{$searchTerm}%");
            });
        }

        // Filters
        if ($request->has('filter')) {
            foreach ($request->filter as $field => $value) {
                if ($value) {
                    $query->where($field, 'LIKE', "%{$value}%");
                }
            }
        }

        // Sorting
        if ($request->has('sort_field') && $request->sort_field) {
            $query->orderBy(
                $request->sort_field,
                $request->sort_order ?? 'asc'
            );
        } else {
            $query->latest();
        }

        if ($request->has('type_membre')) {

            $typePeriode = $request->input('type_membre', 'semestriel');

            $query->where('type', 'LIKE', "%{$typePeriode}%");
        } else {
            $query->latest();
        }



        // Pagination
        $perPage = $request->per_page ?? 10;
        $periodes = $query->paginate($perPage);

        return sendResponse($periodes, 'Liste des periodes récupérée avec succès.');
    }

    public function store(PeriodeStoreRequest $request)
    {
        $periode = Periode::create($request->validated());

        return sendResponse($periode, 'Periode créée avec succès.');
    }

    public function show(Request $request, Periode $periode)
    {
        return sendResponse($periode, 'Détails de la periode récupérés avec succès.');
    }

    public function update(PeriodeUpdateRequest $request, Periode $periode)
    {
        $periode->update($request->validated());

        return sendResponse($periode, 'Periode mise à jour avec succès.');
    }

    public function destroy(Request $request, Periode $periode)
    {
        $periode->delete();

        return sendResponse(null, 'Periode supprimée avec succès.');
    }
}
