<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Commune extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'code',
        'province',
        'region',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Une commune a plusieurs villages
     */
    public function villages()
    {
        return $this->hasMany(Village::class);
    }

    /**
     * Une commune a plusieurs sites
     */
    public function sites()
    {
        return $this->hasMany(Site::class);
    }

    /**
     * Nombre total de sites dans la commune
     */
    public function getSitesCountAttribute(): int
    {
        return $this->sites()->count();
    }

    /**
     * Nombre de sites actifs dans la commune
     */
    public function getSitesActifsCountAttribute(): int
    {
        return $this->sites()->where('statut', 'actif')->count();
    }

    /**
     * Scope pour récupérer uniquement les communes actives
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope pour filtrer par province
     */
    public function scopeByProvince($query, string $province)
    {
        return $query->where('province', $province);
    }

    /**
     * Nom complet avec province
     */
    public function getNomCompletAttribute(): string
    {
        return "{$this->nom} ({$this->province})";
    }
}
