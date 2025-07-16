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

        Schema::create('membres', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->string('matricule', 50)->unique();
            $table->string('nom', 100);
            $table->string('prenom', 100);
            $table->string('email', 150);
            $table->string('telephone', 20);
            $table->foreignId('categorie_id')->constrained('categories');
            $table->enum('statut', ["actif","inactif","suspendu"]);
            $table->date('date_adhesion');
            $table->timestamps();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('membres');
    }
};
