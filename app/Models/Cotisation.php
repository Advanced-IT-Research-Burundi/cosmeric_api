<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cotisation extends Model
{
    use HasFactory;

    protected $with = ['membre'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'membre_id',
        'matricule',
        'nom',
        'prenom',
        'nomero_dossier',
        'global',
        'regle',
        'restant',
        'retenu',
        'montant',
        'devise',
        'date_paiement',
        'statut',
        'mode_paiement',
        'reference_paiement',
        'is_import',
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
            'membre_id' => 'integer',
            'montant' => 'decimal:2',
            'date_paiement' => 'date',
        ];
    }

    public function membre(): BelongsTo
    {
        return $this->belongsTo(Membre::class);
    }

}
