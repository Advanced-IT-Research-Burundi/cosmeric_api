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

        Schema::create('credits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('membre_id')->constrained();
            $table->decimal('montant_demande', 10, 2);
            $table->decimal('montant_accorde', 10, 2);
            $table->decimal('taux_interet', 5, 2)->default(3.00);
            $table->integer('duree_mois')->default(12);
            $table->decimal('montant_total_rembourser', 10, 2);
            $table->decimal('montant_mensualite', 10, 2);
            $table->date('date_demande');
            $table->date('date_approbation');
            $table->enum('statut', ["en_attente","approuve","rejete","en_cours","termine"]);
            $table->text('motif');
            $table->timestamps();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('credits');
    }
};
