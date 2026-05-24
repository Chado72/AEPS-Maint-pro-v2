<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SparePart extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'reference',
        'description',
        'categorie',
        'unite_mesure',
        'stock_actuel',
        'stock_minimum',
        'stock_maximum',
        'prix_unitaire',
        'fournisseur',
        'observations',
        'is_active',
    ];

    protected $casts = [
        'stock_actuel' => 'integer',
        'stock_minimum' => 'integer',
        'stock_maximum' => 'integer',
        'prix_unitaire' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * Une pièce peut être utilisée dans plusieurs interventions
     */
    public function interventionPieces()
    {
        return $this->hasMany(InterventionPiece::class);
    }

    /**
     * Interventions où cette pièce a été utilisée
     */
    public function interventions()
    {
        return $this->belongsToMany(Intervention::class, 'intervention_pieces')
            ->withPivot('quantite_utilisee', 'prix_unitaire_applique')
            ->withTimestamps();
    }

    /**
     * Scope pour récupérer uniquement les pièces actives
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope pour filtrer par catégorie
     */
    public function scopeByCategorie($query, string $categorie)
    {
        return $query->where('categorie', $categorie);
    }

    /**
     * Vérifier si le stock est faible
     */
    public function getStockFaibleAttribute(): bool
    {
        return $this->stock_actuel <= $this->stock_minimum;
    }

    /**
     * Vérifier si le stock est critique (zéro ou négatif)
     */
    public function getStockCritiqueAttribute(): bool
    {
        return $this->stock_actuel <= 0;
    }

    /**
     * Label du statut de stock pour affichage
     */
    public function getStockStatutLabelAttribute(): string
    {
        if ($this->stock_critique) {
            return 'Critique';
        } elseif ($this->stock_faible) {
            return 'Faible';
        } else {
            return 'Suffisant';
        }
    }

    /**
     * Couleur Bootstrap selon le statut de stock
     */
    public function getStockStatutColorAttribute(): string
    {
        if ($this->stock_critique) {
            return 'danger';
        } elseif ($this->stock_faible) {
            return 'warning';
        } else {
            return 'success';
        }
    }

    /**
     * Label de catégorie pour affichage
     */
    public function getCategorieLabelAttribute(): string
    {
        $labels = [
            'pompe' => 'Pompe',
            'moteur' => 'Moteur',
            'panneau_solaire' => 'Panneau solaire',
            'batterie' => 'Batterie',
            'robinet' => 'Robinet',
            'vanne' => 'Vanne',
            'tuyauterie' => 'Tuyauterie',
            'electrique' => 'Électrique',
            'autre' => 'Autre',
        ];
        return $labels[$this->categorie] ?? ucfirst($this->categorie);
    }

    /**
     * Valeur totale du stock (stock_actuel * prix_unitaire)
     */
    public function getValeurStockAttribute(): float
    {
        return $this->stock_actuel * $this->prix_unitaire;
    }
}
