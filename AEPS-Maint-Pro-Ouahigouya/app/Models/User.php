<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'role_id',
        'nom',
        'prenom',
        'email',
        'telephone',
        'password',
        'matricule',
        'service',
        'is_active',
        'last_login_at',
        'last_login_ip',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'last_login_at' => 'datetime',
    ];

    /**
     * Un utilisateur appartient à un rôle
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Un utilisateur peut créer plusieurs interventions
     */
    public function interventions()
    {
        return $this->hasMany(Intervention::class);
    }

    /**
     * Un utilisateur peut uploader plusieurs documents
     */
    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    /**
     * Un utilisateur a plusieurs sessions IA
     */
    public function aiSessions()
    {
        return $this->hasMany(AiSession::class);
    }

    /**
     * Un utilisateur a plusieurs logs d'audit
     */
    public function auditLogs()
    {
        return $this->hasMany(AuditLog::class);
    }

    /**
     * Vérifier si l'utilisateur a un rôle spécifique
     */
    public function hasRole(string $roleSlug): bool
    {
        return $this->role && $this->role->slug === $roleSlug;
    }

    /**
     * Vérifier si l'utilisateur est administrateur
     */
    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    /**
     * Vérifier si l'utilisateur est technicien
     */
    public function isTechnician(): bool
    {
        return $this->hasRole('technicien');
    }

    /**
     * Nom complet de l'utilisateur
     */
    public function getNomCompletAttribute(): string
    {
        return strtoupper($this->nom) . ' ' . ucfirst($this->prenom);
    }

    /**
     * Scope pour récupérer uniquement les utilisateurs actifs
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
