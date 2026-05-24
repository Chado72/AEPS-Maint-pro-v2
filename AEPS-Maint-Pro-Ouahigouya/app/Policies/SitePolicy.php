<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Site;

class SitePolicy
{
    /**
     * Déterminer si l'utilisateur peut voir la liste des sites.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->role?->hasPermission('view_all');
    }

    /**
     * Déterminer si l'utilisateur peut voir un site spécifique.
     */
    public function view(User $user, Site $site): bool
    {
        return $user->isAdmin() || $user->role?->hasPermission('view_all');
    }

    /**
     * Déterminer si l'utilisateur peut créer des sites.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->role?->hasPermission('create_site');
    }

    /**
     * Déterminer si l'utilisateur peut mettre à jour un site.
     */
    public function update(User $user, Site $site): bool
    {
        return $user->isAdmin() || $user->role?->hasPermission('update_site');
    }

    /**
     * Déterminer si l'utilisateur peut supprimer un site.
     */
    public function delete(User $user, Site $site): bool
    {
        return $user->isAdmin() || $user->role?->hasPermission('delete_site');
    }

    /**
     * Déterminer si l'utilisateur peut exporter un site en PDF.
     */
    public function exportPdf(User $user, Site $site): bool
    {
        return $user->isAdmin() || $user->role?->hasPermission('generate_reports');
    }
}
