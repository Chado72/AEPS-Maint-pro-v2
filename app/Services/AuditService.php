<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

class AuditService
{
    /**
     * Enregistre une action dans les logs d'audit
     *
     * @param string $action Type d'action (create, update, delete, login, etc.)
     * @param string $modelType Classe du modèle concerné
     * @param int|null $modelId ID de l'entité concernée
     * @param array $details Détails supplémentaires en JSON
     * @return AuditLog
     */
    public function log(string $action, string $modelType, ?int $modelId = null, array $details = []): AuditLog
    {
        return AuditLog::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'model_type' => $modelType,
            'model_id' => $modelId,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'details' => $details,
        ]);
    }

    /**
     * Récupère l'historique d'un modèle spécifique
     *
     * @param string $modelType Classe du modèle
     * @param int $modelId ID de l'entité
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getModelHistory(string $modelType, int $modelId)
    {
        return AuditLog::where('model_type', $modelType)
            ->where('model_id', $modelId)
            ->with('user')
            ->orderByDesc('created_at')
            ->get();
    }

    /**
     * Récupère les actions récentes d'un utilisateur
     *
     * @param int $userId ID de l'utilisateur
     * @param int $limit Nombre maximum de résultats
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getUserActions(int $userId, int $limit = 50)
    {
        return AuditLog::where('user_id', $userId)
            ->with(['auditable'])
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();
    }

    /**
     * Récupère toutes les actions récentes du système
     *
     * @param int $limit Nombre maximum de résultats
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getRecentActions(int $limit = 100)
    {
        return AuditLog::with(['user', 'auditable'])
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();
    }

    /**
     * Nettoie les anciens logs (plus vieux que X jours)
     *
     * @param int $days Nombre de jours à conserver
     * @return int Nombre de lignes supprimées
     */
    public function cleanupOldLogs(int $days = 365): int
    {
        $cutoffDate = now()->subDays($days);
        
        return AuditLog::where('created_at', '<', $cutoffDate)->delete();
    }
}
