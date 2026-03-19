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
        Schema::table('remboursements', function (Blueprint $table) {
            // Informations du membre
            $table->string('matricule')->nullable()->after('credit_id');
            $table->string('nom')->nullable()->after('matricule');
            $table->string('prenom')->nullable()->after('nom');

            // Colonnes d'importation (comme cotisation_mensuelles)
            $table->string('nomero_dossier')->nullable()->after('prenom');
            $table->string('global')->nullable()->after('nomero_dossier');
            $table->string('regle')->nullable()->after('global');
            $table->string('restant')->nullable()->after('regle');
            $table->string('retenu')->nullable()->after('restant');
            $table->boolean('is_import')->default(false)->after('preuve_paiement');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('remboursements', function (Blueprint $table) {
            $table->dropColumn([
                'matricule',
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
