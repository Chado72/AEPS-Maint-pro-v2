<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Village;

class VillagePolicy
{
    /**
     * Déterminer si l'utilisateur peut voir la liste des villages.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->role?->hasPermission('view_all');
    }

    /**
     * Déterminer si l'utilisateur peut voir un village spécifique.
     */
    public function view(User $user, Village $village): bool
    {
        return $user->isAdmin() || $user->role?->hasPermission('view_all');
    }

    /**
     * Déterminer si l'utilisateur peut créer des villages.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->role?->hasPermission('create_village');
    }

    /**
     * Déterminer si l'utilisateur peut mettre à jour un village.
     */
    public function update(User $user, Village $village): bool
    {
        return $user->isAdmin() || $user->role?->hasPermission('update_village');
    }

    /**
     * Déterminer si l'utilisateur peut supprimer un village.
     */
    public function delete(User $user, Village $village): bool
    {
        return $user->isAdmin() || $user->role?->hasPermission('delete_village');
    }
}
