<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InterventionPiece extends Model
{
    use HasFactory;

    protected $fillable = [
        'intervention_id',
        'spare_part_id',
        'quantite_utilisee',
        'prix_unitaire_applique',
        'observations',
    ];

    protected $casts = [
        'quantite_utilisee' => 'integer',
        'prix_unitaire_applique' => 'decimal:2',
    ];

    /**
     * Une ligne de pièce appartient à une intervention
     */
    public function intervention()
    {
        return $this->belongsTo(Intervention::class);
    }

    /**
     * Une ligne de pièce référence une pièce du stock
     */
    public function sparePart()
    {
        return $this->belongsTo(SparePart::class);
    }

    /**
     * Coût total pour cette ligne (quantité * prix)
     */
    public function getCoutTotalAttribute(): float
    {
        $prix = $this->prix_unitaire_applique ?? $this->sparePart->prix_unitaire ?? 0;
        return $this->quantite_utilisee * $prix;
    }

    /**
     * Boot method pour gérer automatiquement le stock
     */
    public static function boot()
    {
        parent::boot();

        // Décrémenter le stock lors de la création
        static::created(function ($interventionPiece) {
            $sparePart = $interventionPiece->sparePart;
            if ($sparePart) {
                $sparePart->decrement('stock_actuel', $interventionPiece->quantite_utilisee);
            }
        });

        // Ajuster le stock lors de la mise à jour
        static::updated(function ($interventionPiece) {
            if ($interventionPiece->wasChanged('quantite_utilisee')) {
                $sparePart = $interventionPiece->sparePart;
                if ($sparePart) {
                    $diff = $interventionPiece->quantite_utilisee - $interventionPiece->getOriginal('quantite_utilisee');
                    if ($diff > 0) {
                        $sparePart->decrement('stock_actuel', $diff);
                    } elseif ($diff < 0) {
                        $sparePart->increment('stock_actuel', abs($diff));
                    }
                }
            }
        });

        // Réintégrer le stock lors de la suppression
        static::deleted(function ($interventionPiece) {
            $sparePart = $interventionPiece->sparePart;
            if ($sparePart) {
                $sparePart->increment('stock_actuel', $interventionPiece->quantite_utilisee);
            }
        });
    }
}
