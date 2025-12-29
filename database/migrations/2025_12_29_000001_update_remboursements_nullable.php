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
            $table->decimal('montant_paye', 10, 2)
                ->default(0.00)
                ->nullable()
                ->change();

            $table->date('date_paiement')
                ->nullable()
                ->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('remboursements', function (Blueprint $table) {
            $table->decimal('montant_paye', 10, 2)
                ->default(0.00)
                ->nullable(false)
                ->change();

            $table->date('date_paiement')
                ->nullable(false)
                ->change();
        });
    }
};


