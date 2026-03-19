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
        'matricule',
        'nom',
        'prenom',
        'nomero_dossier',
        'global',
        'regle',
        'restant',
        'retenu',
        'numero_echeance',
        'montant_prevu',
        'montant_paye',
        'date_echeance',
        'date_paiement',
        'statut',
        'penalite',
        'preuve_paiement',
        'is_import',
    ];

    /**
     * The attributes that should be appends.
     *
     * @var array
     */
    protected $appends = [
        'preuve_paiement_url',
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

    /**
     * Get the full URL for the proof of payment.
     *
     * @return string|null
     */
    public function getPreuvePaiementUrlAttribute(): ?string
    {
        return $this->preuve_paiement ? asset($this->preuve_paiement) : null;
    }
}
