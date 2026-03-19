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
            // Rendre membre_id nullable
            $table->unsignedBigInteger('membre_id')->nullable()->change();

            // Ajouter colonne matricule nullable
            $table->string('matricule')->nullable()->after('membre_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cotisations', function (Blueprint $table) {
            $table->dropColumn('matricule');
            $table->unsignedBigInteger('membre_id')->nullable(false)->change();
        });
    }
};
