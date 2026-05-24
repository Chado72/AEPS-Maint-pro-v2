<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EnergySource extends Model
{
    use HasFactory;

    protected $fillable = [
        'site_id',
        'type',
        'marque',
        'modele',
        'puissance',
        'date_installation',
        'derniere_maintenance',
        'statut',
        'observations',
        'is_active',
    ];

    protected $casts = [
        'date_installation' => 'date',
        'derniere_maintenance' => 'date',
        'puissance' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Une source d'énergie appartient à un site
     */
    public function site()
    {
        return $this->belongsTo(Site::class);
    }

    /**
     * Une source d'énergie solaire a un système solaire détaillé
     */
    public function solarSystem()
    {
        return $this->hasOne(SolarSystem::class);
    }

    /**
     * Scope pour récupérer uniquement les sources actives
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope pour filtrer par site
     */
    public function scopeBySite($query, int $siteId)
    {
        return $query->where('site_id', $siteId);
    }

    /**
     * Scope pour filtrer par type
     */
    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Label du type pour affichage
     */
    public function getTypeLabelAttribute(): string
    {
        $labels = [
            'solaire' => 'Solaire',
            'electrique' => 'Électrique',
            'diesel' => 'Diesel',
            'eolien' => 'Éolien',
            'manuel' => 'Manuel',
            'gravitaire' => 'Gravitaire',
        ];
        return $labels[$this->type] ?? ucfirst($this->type);
    }

    /**
     * Label du statut pour affichage
     */
    public function getStatutLabelAttribute(): string
    {
        $labels = [
            'operationnel' => 'Opérationnel',
            'en_panne' => 'En panne',
            'maintenance' => 'En maintenance',
            'remplace' => 'Remplacé',
        ];
        return $labels[$this->statut] ?? $this->statut;
    }

    /**
     * Couleur Bootstrap selon le statut
     */
    public function getStatutColorAttribute(): string
    {
        $colors = [
            'operationnel' => 'success',
            'en_panne' => 'danger',
            'maintenance' => 'warning',
            'remplace' => 'secondary',
        ];
        return $colors[$this->statut] ?? 'info';
    }

    /**
     * Puissance avec unité
     */
    public function getPuissanceWithUnitAttribute(): ?string
    {
        if (!$this->puissance) {
            return null;
        }
        
        if ($this->type === 'diesel') {
            return "{$this->puissance} CV";
        }
        
        return "{$this->puissance} W";
    }
}
