<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Schema;

class Credit extends Model
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
        'montant_demande',
        'montant_accorde',
        'taux_interet',
        'duree_mois',
        'montant_total_rembourser',
        'montant_mensualite',
        'date_demande',
        'date_approbation',
        'date_fin',
        'statut',
        'motif',
        'commentaire',
        'user_id',
        'created_by',
        'approved_by',
        'rejected_by'
    ];

    public static function boot()
    {
        parent::boot();
        addColumnIfNotExists('credits', 'approved_by', 'foreignId', 'deleted_at');
        // add column created_by if it doesn't exist
        static::creating(function ($credit) {
            // Custom logic before creating a Credit
            addColumnIfNotExists('credits', 'user_id', 'foreignId', 'created_by');
            addColumnIfNotExists('credits', 'created_by', 'foreignId', 'deleted_at');
            addColumnIfNotExists('credits', 'commentaire', 'text', 'deleted_at');
            addColumnIfNotExists('credits', 'rejected_by', 'foreignId', 'deleted_at');
            addColumnIfNotExists('credits', 'approved_by', 'foreignId', 'deleted_at');
        });

        static::updating(function ($credit) {
            // Custom logic before updating a Credit
        });
    }

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
            'montant_demande' => 'decimal:2',
            'montant_accorde' => 'decimal:2',
            'taux_interet' => 'decimal:2',
            'montant_total_rembourser' => 'decimal:2',
            'montant_mensualite' => 'decimal:2',
            'date_demande' => 'date',
            'date_approbation' => 'date',
        ];
    }

    public function membre(): BelongsTo
    {
        return $this->belongsTo(Membre::class);
    }

    public function remboursements(): HasMany
    {
        return $this->hasMany(Remboursement::class);
    }

   // public static function 
}
