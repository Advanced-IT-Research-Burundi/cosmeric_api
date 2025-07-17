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

        Schema::create('rapports', function (Blueprint $table) {
            $table->id();
            $table->string('titre', 200);
            $table->enum('type_rapport', ["mensuel","semestriel","annuel","personnalise"]);
            $table->date('periode_debut');
            $table->date('periode_fin');
            $table->foreignId('genere_par')->nullable();
            $table->string('fichier_path', 255);
            $table->enum('statut', ["genere","envoye","archive"]);

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
        Schema::dropIfExists('rapports');
    }
};
