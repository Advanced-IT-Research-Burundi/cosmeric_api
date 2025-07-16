<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Rapport extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'titre',
        'type_rapport',
        'periode_debut',
        'periode_fin',
        'genere_par',
        'fichier_path',
        'statut',
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
            'periode_debut' => 'date',
            'periode_fin' => 'date',
            'genere_par' => 'integer',
            'created_at' => 'timestamp',
        ];
    }

    public function generePar(): BelongsTo
    {
        return $this->belongsTo(GenerePar::class);
    }
}
