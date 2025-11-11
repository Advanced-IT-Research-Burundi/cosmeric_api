<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ImportationController extends Controller
{
    //
    public function cotisation(Request $request){
        return $request->all();
    }
}
