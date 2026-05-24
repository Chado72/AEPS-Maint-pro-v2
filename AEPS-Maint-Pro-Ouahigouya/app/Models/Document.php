<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;

    protected $fillable = [
        'site_id',
        'forage_id',
        'intervention_id',
        'nom_fichier',
        'nom_original',
        'chemin_fichier',
        'mime_type',
        'taille_octets',
        'type',
        'description',
        'user_id',
        'is_public',
        'is_active',
    ];

    protected $casts = [
        'taille_octets' => 'integer',
        'is_public' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * Un document peut être associé à un site
     */
    public function site()
    {
        return $this->belongsTo(Site::class);
    }

    /**
     * Un document peut être associé à un forage
     */
    public function forage()
    {
        return $this->belongsTo(Forage::class);
    }

    /**
     * Un document peut être associé à une intervention
     */
    public function intervention()
    {
        return $this->belongsTo(Intervention::class);
    }

    /**
     * Un document est uploadé par un utilisateur
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope pour récupérer uniquement les documents actifs
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope pour filtrer par type
     */
    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope pour filtrer par site
     */
    public function scopeBySite($query, int $siteId)
    {
        return $query->where('site_id', $siteId);
    }

    /**
     * Scope pour filtrer par intervention
     */
    public function scopeByIntervention($query, int $interventionId)
    {
        return $query->where('intervention_id', $interventionId);
    }

    /**
     * Label du type pour affichage
     */
    public function getTypeLabelAttribute(): string
    {
        $labels = [
            'photo' => 'Photo',
            'rapport' => 'Rapport',
            'facture' => 'Facture',
            'plan' => 'Plan',
            'manuel' => 'Manuel',
            'autre' => 'Autre',
        ];
        return $labels[$this->type] ?? ucfirst($this->type);
    }

    /**
     * Taille du fichier formatée (Ko, Mo, etc.)
     */
    public function getTailleFormateeAttribute(): string
    {
        $bytes = $this->taille_octets;
        $units = ['o', 'Ko', 'Mo', 'Go', 'To'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * URL complète du document
     */
    public function getUrlAttribute(): string
    {
        return asset('storage/' . $this->chemin_fichier);
    }

    /**
     * Vérifier si le document est une image
     */
    public function getIsImageAttribute(): bool
    {
        return str_starts_with($this->mime_type, 'image/');
    }

    /**
     * Vérifier si le document est un PDF
     */
    public function getIsPdfAttribute(): bool
    {
        return $this->mime_type === 'application/pdf';
    }
}
