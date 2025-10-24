<?php

namespace App\Http\Controllers;

use App\Models\Cotisation;
use App\Models\Credit;
use App\Models\Membre;
use App\Models\Remboursement;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    //
    public function index(){
        return response()->json([
            'success' => true,
            'data' => [
                'membres' => Membre::count(),
                'cotisations' => Cotisation::count(),
                'credits' => Credit::count(),
                'remboursements' => Remboursement::count(),

                'users' => User::count(),
            ],
        ]);
    }
}
