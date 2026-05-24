<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'action',
        'model_type',
        'model_id',
        'model_name',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
        'description',
    ];

    protected $casts = [
        'model_id' => 'integer',
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    /**
     * Un log d'audit appartient à un utilisateur (peut être null si action système)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope pour filtrer par utilisateur
     */
    public function scopeByUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope pour filtrer par action
     */
    public function scopeByAction($query, string $action)
    {
        return $query->where('action', $action);
    }

    /**
     * Scope pour filtrer par modèle
     */
    public function scopeByModel($query, string $modelType, int $modelId = null)
    {
        $query = $query->where('model_type', $modelType);
        
        if ($modelId) {
            $query = $query->where('model_id', $modelId);
        }
        
        return $query;
    }

    /**
     * Scope pour filtrer par période
     */
    public function scopeByPeriod($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Scope pour récupérer les actions critiques
     */
    public function scopeCritical($query)
    {
        $criticalActions = ['delete', 'login_failed', 'permission_change', 'role_change'];
        return $query->whereIn('action', $criticalActions);
    }

    /**
     * Label de l'action pour affichage
     */
    public function getActionLabelAttribute(): string
    {
        $labels = [
            'create' => 'Création',
            'store' => 'Création',
            'update' => 'Modification',
            'delete' => 'Suppression',
            'destroy' => 'Suppression',
            'login' => 'Connexion',
            'logout' => 'Déconnexion',
            'login_failed' => 'Échec connexion',
            'password_change' => 'Changement mot de passe',
            'permission_change' => 'Changement permission',
            'role_change' => 'Changement de rôle',
            'export' => 'Export',
            'import' => 'Import',
        ];
        
        return $labels[$this->action] ?? ucfirst(str_replace('_', ' ', $this->action));
    }

    /**
     * Couleur Bootstrap selon le type d'action
     */
    public function getActionColorAttribute(): string
    {
        $colors = [
            'create' => 'success',
            'store' => 'success',
            'update' => 'info',
            'delete' => 'danger',
            'destroy' => 'danger',
            'login' => 'primary',
            'logout' => 'secondary',
            'login_failed' => 'warning',
        ];
        
        return $colors[$this->action] ?? 'secondary';
    }

    /**
     * Description formatée des changements
     */
    public function getChangesDescriptionAttribute(): ?string
    {
        if (!$this->old_values || !$this->new_values) {
            return null;
        }

        $changes = [];
        
        // Trouver les champs modifiés
        $allKeys = array_unique(array_merge(
            array_keys($this->old_values ?? []),
            array_keys($this->new_values ?? [])
        ));

        foreach ($allKeys as $key) {
            $old = $this->old_values[$key] ?? null;
            $new = $this->new_values[$key] ?? null;
            
            if ($old !== $new) {
                $changes[] = "{$key}: " . (is_null($old) ? 'null' : $old) . " → " . (is_null($new) ? 'null' : $new);
            }
        }

        return implode(', ', $changes);
    }

    /**
     * Enregistrer une action d'audit
     */
    public static function log(
        string $action,
        ?int $userId = null,
        ?string $modelType = null,
        ?int $modelId = null,
        ?string $modelName = null,
        ?array $oldValues = null,
        ?array $newValues = null,
        ?string $description = null
    ): self {
        return self::create([
            'user_id' => $userId,
            'action' => $action,
            'model_type' => $modelType,
            'model_id' => $modelId,
            'model_name' => $modelName,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'description' => $description,
        ]);
    }
}
