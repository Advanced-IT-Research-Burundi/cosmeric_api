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
            // Informations du membre
            $table->string('nom')->nullable()->after('matricule');
            $table->string('prenom')->nullable()->after('nom');

            // Colonnes d'importation (comme cotisation_mensuelles)
            $table->string('nomero_dossier')->nullable()->after('prenom');
            $table->string('global')->nullable()->after('nomero_dossier');
            $table->string('regle')->nullable()->after('global');
            $table->string('restant')->nullable()->after('regle');
            $table->string('retenu')->nullable()->after('restant');
            $table->boolean('is_import')->default(false)->after('reference_paiement');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cotisations', function (Blueprint $table) {
            $table->dropColumn([
                'nom',
                'prenom',
                'nomero_dossier',
                'global',
                'regle',
                'restant',
                'retenu',
                'is_import',
            ]);
        });
    }
};
