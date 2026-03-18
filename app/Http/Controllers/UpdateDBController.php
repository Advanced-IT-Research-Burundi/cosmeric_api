<?php

namespace App\Http\Controllers;


class UpdateDBController extends Controller
{
    //

    public function updateDatabase(){

        // update table assistances to add 'en_cours' status if not exists
        \DB::statement("ALTER TABLE assistances MODIFY statut ENUM('en_attente','en_cours', 'approuve', 'rejete', 'verse')");

    }
}
