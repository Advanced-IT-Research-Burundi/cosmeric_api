<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Assistance extends Model
{
    use HasFactory;

    protected $with = ['membre', 'typeAssistance'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'membre_id',
        'type_assistance_id',
        'montant',
        'date_demande',
        'date_approbation',
        'date_versement',
        'statut',
        'justificatif',
        'motif_rejet',
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
            'type_assistance_id' => 'integer',
            'montant' => 'decimal:2',
            'date_demande' => 'date',
            'date_approbation' => 'date',
            'date_versement' => 'date',
        ];
    }

    public function membre(): BelongsTo
    {
        return $this->belongsTo(Membre::class);
    }

    public function typeAssistance(): BelongsTo
    {
        return $this->belongsTo(TypeAssistance::class);
    }

    public function getJustificatifAttribute($value)
    {
        return asset( $value);
    }
}
