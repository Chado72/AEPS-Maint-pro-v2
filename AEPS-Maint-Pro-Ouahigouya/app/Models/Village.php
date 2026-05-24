<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Village extends Model
{
    use HasFactory;

    protected $fillable = [
        'commune_id',
        'nom',
        'code',
        'latitude',
        'longitude',
        'population',
        'description',
        'is_active',
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'population' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Un village appartient à une commune
     */
    public function commune()
    {
        return $this->belongsTo(Commune::class);
    }

    /**
     * Un village a plusieurs sites
     */
    public function sites()
    {
        return $this->hasMany(Site::class);
    }

    /**
     * Scope pour récupérer uniquement les villages actifs
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope pour filtrer par commune
     */
    public function scopeByCommune($query, int $communeId)
    {
        return $query->where('commune_id', $communeId);
    }

    /**
     * Coordonnées GPS formatées
     */
    public function getCoordinatesAttribute(): ?string
    {
        if ($this->latitude && $this->longitude) {
            return "{$this->latitude}, {$this->longitude}";
        }
        return null;
    }

    /**
     * Nom complet avec commune
     */
    public function getNomCompletAttribute(): string
    {
        return "{$this->nom} ({$this->commune->nom})";
    }
}
