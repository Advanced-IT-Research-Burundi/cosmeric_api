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
        Schema::table('cotisations', function (Blueprint $table) {
            $table->dropForeign(['periode_id']);
            $table->dropColumn('periode_id');
        });

        Schema::dropIfExists('periodes');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('periodes', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ["mensuel","semestriel"])->nullable();
            $table->integer('mois')->nullable();
            $table->integer('semestre')->nullable();
            $table->year('annee')->nullable();
            $table->enum('statut', ["ouvert","ferme"])->nullable();
            $table->date('date_debut')->nullable();
            $table->date('date_fin');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('cotisations', function (Blueprint $table) {
            $table->foreignId('periode_id')->nullable()->constrained();
        });
    }
};
