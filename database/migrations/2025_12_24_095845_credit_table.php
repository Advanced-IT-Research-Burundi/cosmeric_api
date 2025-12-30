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
            $table->double('montant_demande', 64, 2);
            $table->double('montant_restant', 64, 2)->default(0);
            $table->double('montant_accorde', 64, 2)->default(0);
            $table->double(
                'taux_interet',
                6,
                2
            )->default(3.00);
            $table->integer('duree_mois')->default(12);
            $table->double('montant_total_rembourser', 64, 2);
            $table->double('montant_mensualite', 64, 2);
            $table->date('date_demande')->default(now());
            $table->date('date_approbation')->nullable();
            $table->date('date_fin')->nullable();
            // En cours envoyer envoyer au responsable
            $table->enum('statut', ["en_attente", "approuve", "rejete", "en_cours", "termine"])->default("en_attente");
            $table->text('motif')->nullable();
            $table->text('commentaire')->nullable();
            $table->foreignId('created_by')->constrained('users','id');
            $table->foreignId('user_id')->constrained('users','id');
            $table->foreignId('approved_by')->nullable()->constrained('users','id');
            $table->foreignId('rejected_by')->nullable()->constrained('users','id');
            $table->timestamps();
            $table->softDeletes();
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
