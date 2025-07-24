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
        $search = $request->input('search');
        $attributes = [
            'membre_id',
            'type_assistance_id',
            'montant',
            'date_demande',
            'date_approbation',
            'date_versement',
            'statut',
            'justificatif'
        ];

        $assistances = Assistance::with(['membre', 'typeAssistance'])->when($search, function ($query)use ($attributes , $search) {
            foreach ($attributes as $attribute) {
                $query->orWhere($attribute, 'like', '%' . $search . '%');
            }
        })->latest()->paginate();

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
        return sendResponse($assistance);
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
