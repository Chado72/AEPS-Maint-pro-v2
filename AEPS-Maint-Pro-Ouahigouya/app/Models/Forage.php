<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Forage extends Model
{
    use HasFactory;

    protected $fillable = [
        'site_id',
        'nom',
        'code',
        'profondeur',
        'debit',
        'date_forage',
        'entreprise_forage',
        'statut',
        'caracteristiques_geologiques',
        'observations',
        'is_active',
    ];

    protected $casts = [
        'date_forage' => 'date',
        'profondeur' => 'integer',
        'debit' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Un forage appartient à un site
     */
    public function site()
    {
        return $this->belongsTo(Site::class);
    }

    /**
     * Un forage peut être concerné par plusieurs interventions
     */
    public function interventions()
    {
        return $this->hasMany(Intervention::class);
    }

    /**
     * Un forage peut avoir des documents associés
     */
    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    /**
     * Scope pour récupérer uniquement les forages actifs
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
     * Scope pour filtrer par statut
     */
    public function scopeByStatut($query, string $statut)
    {
        return $query->where('statut', $statut);
    }

    /**
     * Label du statut pour affichage
     */
    public function getStatutLabelAttribute(): string
    {
        $labels = [
            'operationnel' => 'Opérationnel',
            'en_panne' => 'En panne',
            'tari' => 'Tari',
            'abandonne' => 'Abandonné',
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
            'tari' => 'warning',
            'abandonne' => 'secondary',
        ];
        return $colors[$this->statut] ?? 'info';
    }

    /**
     * Profondeur avec unité
     */
    public function getProfondeurWithUnitAttribute(): ?string
    {
        return $this->profondeur ? "{$this->profondeur} m" : null;
    }

    /**
     * Débit avec unité
     */
    public function getDebitWithUnitAttribute(): ?string
    {
        return $this->debit ? "{$this->debit} m³/h" : null;
    }
}
