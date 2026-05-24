<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Intervention;

class InterventionPolicy
{
    /**
     * Déterminer si l'utilisateur peut voir la liste des interventions.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->role?->hasPermission('view_all');
    }

    /**
     * Déterminer si l'utilisateur peut voir une intervention spécifique.
     * Seul l'admin ou le créateur peut voir l'intervention.
     */
    public function view(User $user, Intervention $intervention): bool
    {
        return $user->isAdmin() || $intervention->user_id === $user->id;
    }

    /**
     * Déterminer si l'utilisateur peut créer des interventions.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->role?->hasPermission('create_intervention');
    }

    /**
     * Déterminer si l'utilisateur peut mettre à jour une intervention.
     * Seul l'admin ou le créateur peut modifier.
     */
    public function update(User $user, Intervention $intervention): bool
    {
        return $user->isAdmin() || $intervention->user_id === $user->id;
    }

    /**
     * Déterminer si l'utilisateur peut supprimer une intervention.
     * Seul l'admin ou le créateur peut supprimer.
     */
    public function delete(User $user, Intervention $intervention): bool
    {
        return $user->isAdmin() || $intervention->user_id === $user->id;
    }

    /**
     * Déterminer si l'utilisateur peut exporter une intervention en PDF.
     */
    public function exportPdf(User $user, Intervention $intervention): bool
    {
        return $user->isAdmin() || $intervention->user_id === $user->id;
    }
}
