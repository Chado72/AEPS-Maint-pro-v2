<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'permissions',
        'is_active',
    ];

    protected $casts = [
        'permissions' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Un rôle a plusieurs utilisateurs
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Vérifier si le rôle a une permission spécifique
     */
    public function hasPermission(string $permission): bool
    {
        $permissions = $this->permissions ?? [];
        return in_array($permission, $permissions);
    }

    /**
     * Ajouter une permission au rôle
     */
    public function addPermission(string $permission): void
    {
        $permissions = $this->permissions ?? [];
        if (!in_array($permission, $permissions)) {
            $permissions[] = $permission;
            $this->update(['permissions' => $permissions]);
        }
    }

    /**
     * Scope pour récupérer uniquement les rôles actifs
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
