<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Laravel\Sanctum\PersonalAccessToken;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'nom',
        'prenom',
        'email',
        'telephone',
        'password',
        'role',
        'email_verified_at',
        'is_active',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'is_active' => 'boolean',
            'password' => 'hashed',
        ];
    }

    // Accesseurs
    protected function fullName(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->prenom . ' ' . $this->nom,
        );
    }

    // Relations
    public function membre()
    {
        return $this->hasOne(Membre::class);
    }

    public function tokens()
    {
        return $this->morphMany(PersonalAccessToken::class, 'tokenable');
    }

    // Méthodes utilitaires
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isGestionnaire(): bool
    {
        return $this->role === 'gestionnaire';
    }

    public function isMembre(): bool
    {
        return $this->role === 'membre';
    }

    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }

    public function hasAnyRole(array $roles): bool
    {
        return in_array($this->role, $roles);
    }

    public function updateLastLogin(): void
    {
        $this->update(['last_login_at' => now()]);
    }
}
