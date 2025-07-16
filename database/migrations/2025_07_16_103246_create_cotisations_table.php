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
        Schema::disableForeignKeyConstraints();

        Schema::create('cotisations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('membre_id')->constrained();
            $table->foreignId('periode_id')->constrained();
            $table->decimal('montant', 10, 2);
            $table->enum('devise', ["FBU","USD"]);
            $table->date('date_paiement');
            $table->enum('statut', ["paye","en_attente","en_retard"]);
            $table->string('mode_paiement', 50);
            $table->string('reference_paiement', 100);
            $table->timestamps();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cotisations');
    }
};
