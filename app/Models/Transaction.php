<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'membre_id',
        'type_transaction',
        'reference_transaction',
        'montant',
        'devise',
        'sens',
        'date_transaction',
        'description',
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
            'date_transaction' => 'date',
            'created_at' => 'timestamp',
        ];
    }

    public function membre(): BelongsTo
    {
        return $this->belongsTo(Membre::class);
    }
}
