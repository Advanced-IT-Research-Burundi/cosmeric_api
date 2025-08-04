<?php

namespace App\Http\Controllers;

use App\Http\Requests\AssistanceStoreRequest;
use App\Http\Requests\AssistanceUpdateRequest;
use App\Http\Resources\AssistanceCollection;
use App\Http\Resources\AssistanceResource;
use App\Models\Assistance;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AssistanceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return AssistanceCollection
     */
    public function index(Request $request)
    {
        $query = Assistance::query();

        // Search
        if ($request->has('search') && $request->search) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->whereAny(['membre_id', 'type_assistance_id', 'montant', 'date_demande', 'date_approbation', 'date_versement', 'statut', 'justificatif'], 'LIKE', "%{$searchTerm}%");
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
        }

        // Pagination
        $perPage = $request->per_page ?? 10;
        $assistances = $query->paginate($perPage);

        return sendResponse(
            $assistances,
            Response::HTTP_OK
        );
    }

    public function store(AssistanceStoreRequest $request)
    {
        $assistance = Assistance::create($request->validated());
        return sendResponse(
            $assistance,
            Response::HTTP_CREATED,
        );
    }

    public function show(Request $request, Assistance $assistance)
    {
        return sendResponse($assistance, 'Détails de l\'assistance récupérés avec succès.');
    }

    public function update(AssistanceUpdateRequest $request, Assistance $assistance)
    {
        $assistance->update($request->validated());

        return new AssistanceResource($assistance);
    }

    public function destroy(Request $request, Assistance $assistance)
    {
        $assistance->delete();

        return response()->noContent();
    }
}
