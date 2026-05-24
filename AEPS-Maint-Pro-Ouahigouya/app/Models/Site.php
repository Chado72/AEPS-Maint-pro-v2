<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Site extends Model
{
    use HasFactory;

    protected $fillable = [
        'commune_id',
        'village_id',
        'nom',
        'code',
        'type',
        'statut',
        'date_mise_en_service',
        'capacite_reservoir',
        'nombre_robinets',
        'latitude',
        'longitude',
        'description',
        'observations',
        'is_active',
    ];

    protected $casts = [
        'date_mise_en_service' => 'date',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'nombre_robinets' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Un site appartient à une commune
     */
    public function commune()
    {
        return $this->belongsTo(Commune::class);
    }

    /**
     * Un site appartient à un village
     */
    public function village()
    {
        return $this->belongsTo(Village::class);
    }

    /**
     * Un site peut avoir plusieurs forages (règle métier importante)
     */
    public function forages()
    {
        return $this->hasMany(Forage::class);
    }

    /**
     * Un site peut avoir plusieurs sources d'énergie
     */
    public function energySources()
    {
        return $this->hasMany(EnergySource::class);
    }

    /**
     * Un site a plusieurs interventions
     */
    public function interventions()
    {
        return $this->hasMany(Intervention::class);
    }

    /**
     * Un site a plusieurs documents
     */
    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    /**
     * Récupérer le forage principal (premier forage opérationnel)
     */
    public function getForagePrincipalAttribute(): ?Forage
    {
        return $this->forages()->where('statut', 'operationnel')->first();
    }

    /**
     * Nombre total de forages sur le site
     */
    public function getForagesCountAttribute(): int
    {
        return $this->forages()->count();
    }

    /**
     * Nombre d'interventions sur le site
     */
    public function getInterventionsCountAttribute(): int
    {
        return $this->interventions()->count();
    }

    /**
     * Dernière intervention sur le site
     */
    public function getLastInterventionAttribute(): ?Intervention
    {
        return $this->interventions()->latest('date_intervention')->first();
    }

    /**
     * Scope pour récupérer uniquement les sites actifs
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope pour filtrer par type (AEPS ou PEA)
     */
    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope pour filtrer par statut
     */
    public function scopeByStatut($query, string $statut)
    {
        return $query->where('statut', $statut);
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
     * Label du statut pour affichage
     */
    public function getStatutLabelAttribute(): string
    {
        $labels = [
            'actif' => 'Actif',
            'en_panne' => 'En panne',
            'abandonne' => 'Abandonné',
            'en_construction' => 'En construction',
        ];
        return $labels[$this->statut] ?? $this->statut;
    }

    /**
     * Couleur Bootstrap selon le statut
     */
    public function getStatutColorAttribute(): string
    {
        $colors = [
            'actif' => 'success',
            'en_panne' => 'danger',
            'abandonne' => 'secondary',
            'en_construction' => 'warning',
        ];
        return $colors[$this->statut] ?? 'info';
    }
}
