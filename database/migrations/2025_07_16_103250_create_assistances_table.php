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

        Schema::create('assistances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('membre_id')->constrained();
            $table->foreignId('type_assistance_id')->constrained();
            $table->decimal('montant', 10, 2);
            $table->date('date_demande');
            $table->date('date_approbation');
            $table->date('date_versement');
            $table->enum('statut', ["en_attente","approuve","rejete","verse"]);
            $table->string('justificatif', 255);
            $table->text('motif_rejet');
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
        Schema::dropIfExists('assistances');
    }
};
