<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SolarSystem extends Model
{
    use HasFactory;

    protected $fillable = [
        'energy_source_id',
        'nombre_panneaux',
        'puissance_panneau',
        'capacite_batterie',
        'tension',
        'marque_controleur',
        'date_installation_panneaux',
        'date_remplacement_batteries',
        'observations',
    ];

    protected $casts = [
        'nombre_panneaux' => 'integer',
        'puissance_panneau' => 'integer',
        'capacite_batterie' => 'integer',
        'tension' => 'integer',
        'date_installation_panneaux' => 'date',
        'date_remplacement_batteries' => 'date',
    ];

    /**
     * Un système solaire appartient à une source d'énergie
     */
    public function energySource()
    {
        return $this->belongsTo(EnergySource::class);
    }

    /**
     * Puissance totale du système (nombre_panneaux * puissance_panneau)
     */
    public function getPuissanceTotaleAttribute(): ?int
    {
        if ($this->nombre_panneaux && $this->puissance_panneau) {
            return $this->nombre_panneaux * $this->puissance_panneau;
        }
        return null;
    }

    /**
     * Puissance totale avec unité
     */
    public function getPuissanceTotaleWithUnitAttribute(): ?string
    {
        $puissance = $this->puissance_totale;
        return $puissance ? "{$puissance} Wc" : null;
    }

    /**
     * Capacité batterie avec unité
     */
    public function getCapaciteBatterieWithUnitAttribute(): ?string
    {
        return $this->capacite_batterie ? "{$this->capacite_batterie} Ah" : null;
    }

    /**
     * Tension avec unité
     */
    public function getTensionWithUnitAttribute(): ?string
    {
        return $this->tension ? "{$this->tension} V" : null;
    }

    /**
     * Vérifier si les batteries sont récentes (moins de 5 ans)
     */
    public function getBatteriesRecentesAttribute(): bool
    {
        if (!$this->date_remplacement_batteries) {
            return false;
        }
        
        return $this->date_remplacement_batteries->diffInYears(now()) < 5;
    }
}
