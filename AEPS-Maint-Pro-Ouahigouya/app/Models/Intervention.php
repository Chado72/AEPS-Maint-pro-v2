<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Intervention extends Model
{
    use HasFactory;

    protected $fillable = [
        'site_id',
        'forage_id',
        'user_id',
        'type',
        'titre',
        'description',
        'diagnostic',
        'travaux_realises',
        'date_intervention',
        'heure_debut',
        'heure_fin',
        'duree_minutes',
        'cout_main_oeuvre',
        'cout_pieces',
        'cout_total',
        'statut',
        'priorite',
        'recommandations',
        'observations',
        'is_active',
    ];

    protected $casts = [
        'date_intervention' => 'date',
        'heure_debut' => 'datetime:H:i',
        'heure_fin' => 'datetime:H:i',
        'duree_minutes' => 'integer',
        'cout_main_oeuvre' => 'decimal:2',
        'cout_pieces' => 'decimal:2',
        'cout_total' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * Une intervention appartient à un site
     */
    public function site()
    {
        return $this->belongsTo(Site::class);
    }

    /**
     * Une intervention peut concerner un forage spécifique
     */
    public function forage()
    {
        return $this->belongsTo(Forage::class);
    }

    /**
     * Une intervention est réalisée par un utilisateur (technicien)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Une intervention utilise plusieurs pièces
     */
    public function pieces()
    {
        return $this->hasMany(InterventionPiece::class);
    }

    /**
     * Une intervention peut avoir des documents associés
     */
    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    /**
     * Calculer automatiquement le coût total avant sauvegarde
     */
    public static function boot()
    {
        parent::boot();

        static::saving(function ($intervention) {
            $intervention->cout_total = $intervention->cout_main_oeuvre + $intervention->cout_pieces;
        });
    }

    /**
     * Scope pour récupérer uniquement les interventions actives
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
     * Scope pour filtrer par statut
     */
    public function scopeByStatut($query, string $statut)
    {
        return $query->where('statut', $statut);
    }

    /**
     * Scope pour filtrer par période
     */
    public function scopeByPeriod($query, $startDate, $endDate)
    {
        return $query->whereBetween('date_intervention', [$startDate, $endDate]);
    }

    /**
     * Label du type pour affichage
     */
    public function getTypeLabelAttribute(): string
    {
        $labels = [
            'preventive' => 'Préventive',
            'corrective' => 'Corrective',
            'urgence' => 'Urgence',
            'inspection' => 'Inspection',
            'maintenance' => 'Maintenance',
        ];
        return $labels[$this->type] ?? ucfirst($this->type);
    }

    /**
     * Label du statut pour affichage
     */
    public function getStatutLabelAttribute(): string
    {
        $labels = [
            'planifiee' => 'Planifiée',
            'en_cours' => 'En cours',
            'terminee' => 'Terminée',
            'annulee' => 'Annulée',
        ];
        return $labels[$this->statut] ?? $this->statut;
    }

    /**
     * Label de priorité pour affichage
     */
    public function getPrioriteLabelAttribute(): string
    {
        $labels = [
            'faible' => 'Faible',
            'moyenne' => 'Moyenne',
            'haute' => 'Haute',
            'critique' => 'Critique',
        ];
        return $labels[$this->priorite] ?? ucfirst($this->priorite);
    }

    /**
     * Couleur Bootstrap selon la priorité
     */
    public function getPrioriteColorAttribute(): string
    {
        $colors = [
            'faible' => 'info',
            'moyenne' => 'warning',
            'haute' => 'danger',
            'critique' => 'dark',
        ];
        return $colors[$this->priorite] ?? 'secondary';
    }

    /**
     * Durée formatée (heures et minutes)
     */
    public function getDureeFormateeAttribute(): ?string
    {
        if (!$this->duree_minutes) {
            return null;
        }

        $hours = floor($this->duree_minutes / 60);
        $minutes = $this->duree_minutes % 60;

        if ($hours > 0 && $minutes > 0) {
            return "{$hours}h {$minutes}min";
        } elseif ($hours > 0) {
            return "{$hours}h";
        } else {
            return "{$minutes}min";
        }
    }
}
