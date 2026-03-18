<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('categorie_membres', function (Blueprint $table) {
            $table->id();
            $table->string('nom', 100);
            $table->integer('montant_cotisation');
            $table->enum('devise', ["FBU","USD"]);
            $table->enum('frequence_paiement', ["mensuel","semestriel"]);
            $table->text('description');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categorie_membres');
    }
};
