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

        Schema::create('remboursements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('credit_id')->constrained();
            $table->integer('numero_echeance');
            $table->decimal('montant_prevu', 10, 2);
            $table->decimal('montant_paye', 10, 2);
            $table->date('date_echeance');
            $table->date('date_paiement');
            $table->enum('statut', ["prevu","paye","en_retard"]);
            $table->decimal('penalite', 10, 2)->default(0.00);
            $table->timestamps();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('remboursements');
    }
};
