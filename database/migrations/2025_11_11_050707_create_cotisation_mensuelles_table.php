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
        Schema::create('cotisation_mensuelles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('matricule')->nullable();
            $table->string('nomero_dossier')->nullable();
            $table->string('global')->nullable();
            $table->string('regle')->nullable();
            $table->string('restant')->nullable();
            $table->string('retenu')->nullable();
            $table->string('date_cotisation')->nullable();
            $table->integer('user_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cotisation_mensuelles');
    }
};
