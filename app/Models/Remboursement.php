<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Remboursement extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'credit_id',
        'numero_echeance',
        'montant_prevu',
        'montant_paye',
        'date_echeance',
        'date_paiement',
        'statut',
        'penalite',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'credit_id' => 'integer',
            'montant_prevu' => 'decimal:2',
            'montant_paye' => 'decimal:2',
            'date_echeance' => 'date',
            'date_paiement' => 'date',
            'penalite' => 'decimal:2',
        ];
    }

    public function credit(): BelongsTo
    {
        return $this->belongsTo(Credit::class);
    }
}
