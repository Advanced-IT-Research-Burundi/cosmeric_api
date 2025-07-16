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

        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('membre_id')->constrained();
            $table->enum('type_transaction', ["cotisation","credit","remboursement","assistance"]);
            $table->integer('reference_transaction');
            $table->decimal('montant', 10, 2);
            $table->enum('devise', ["FBU","USD"]);
            $table->enum('sens', ["entree","sortie"]);
            $table->date('date_transaction');
            $table->text('description');
            $table->timestamp('created_at');
            $table->timestamps();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
