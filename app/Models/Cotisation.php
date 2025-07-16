<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cotisation extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'membre_id',
        'periode_id',
        'montant',
        'devise',
        'date_paiement',
        'statut',
        'mode_paiement',
        'reference_paiement',
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
            'periode_id' => 'integer',
            'montant' => 'decimal:2',
            'date_paiement' => 'date',
        ];
    }

    public function membre(): BelongsTo
    {
        return $this->belongsTo(Membre::class);
    }

    public function periode(): BelongsTo
    {
        return $this->belongsTo(Periode::class);
    }
}
