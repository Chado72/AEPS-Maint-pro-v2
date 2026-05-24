<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Commune;

class CommunePolicy
{
    /**
     * Déterminer si l'utilisateur peut voir la liste des communes.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->role?->hasPermission('view_all');
    }

    /**
     * Déterminer si l'utilisateur peut voir une commune spécifique.
     */
    public function view(User $user, Commune $commune): bool
    {
        return $user->isAdmin() || $user->role?->hasPermission('view_all');
    }

    /**
     * Déterminer si l'utilisateur peut créer des communes.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->role?->hasPermission('create_commune');
    }

    /**
     * Déterminer si l'utilisateur peut mettre à jour une commune.
     */
    public function update(User $user, Commune $commune): bool
    {
        return $user->isAdmin() || $user->role?->hasPermission('update_commune');
    }

    /**
     * Déterminer si l'utilisateur peut supprimer une commune.
     */
    public function delete(User $user, Commune $commune): bool
    {
        return $user->isAdmin() || $user->role?->hasPermission('delete_commune');
    }
}
