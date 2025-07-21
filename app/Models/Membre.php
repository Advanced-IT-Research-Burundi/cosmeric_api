<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Membre extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'matricule',
        'nom',
        'prenom',
        'email',
        'telephone',
        'categorie_id',
        'statut',
        'date_adhesion',
    ];

    public static function boot()
    {
        parent::boot();
        static::creating(function ($membre) {
            // Automatically set the user_id if not provided
            if (!$membre->user_id) {
                $membre->user_id = auth()->id();
            }
        });
        static::updating(function ($membre) {
            // Ensure the user_id is set on update
            if (!$membre->user_id) {
                $membre->user_id = auth()->id();
            }
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
            'user_id' => 'integer',
            'categorie_id' => 'integer',
            'date_adhesion' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function categorie(): BelongsTo
    {
        return $this->belongsTo(Categorie::class);
    }
}
