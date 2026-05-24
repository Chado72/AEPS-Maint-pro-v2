<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AiSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'session_id',
        'messages',
        'token_count',
        'provider',
        'model_used',
        'is_active',
        'last_activity_at',
    ];

    protected $casts = [
        'messages' => 'array',
        'token_count' => 'integer',
        'is_active' => 'boolean',
        'last_activity_at' => 'datetime',
    ];

    /**
     * Une session IA appartient à un utilisateur
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope pour récupérer uniquement les sessions actives
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope pour filtrer par utilisateur
     */
    public function scopeByUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Ajouter un message à l'historique
     */
    public function addMessage(string $role, string $content): void
    {
        $messages = $this->messages ?? [];
        $messages[] = [
            'role' => $role,
            'content' => $content,
            'timestamp' => now()->toIso8601String(),
        ];

        // Limiter le nombre de messages gardés en contexte
        $maxMessages = config('ai.max_context_messages', 10);
        if (count($messages) > $maxMessages) {
            $messages = array_slice($messages, -$maxMessages);
        }

        $this->update([
            'messages' => $messages,
            'last_activity_at' => now(),
        ]);
    }

    /**
     * Récupérer l'historique formaté pour l'API
     */
    public function getMessagesForApiAttribute(): array
    {
        $messages = $this->messages ?? [];
        
        // Ajouter le message système au début
        $systemPrompt = config('ai.system_prompt');
        $formattedMessages = [['role' => 'system', 'content' => $systemPrompt]];
        
        // Ajouter les messages de la session
        foreach ($messages as $msg) {
            if (is_array($msg)) {
                $formattedMessages[] = [
                    'role' => $msg['role'] ?? 'user',
                    'content' => $msg['content'] ?? '',
                ];
            } else {
                // Format legacy (string simple)
                $formattedMessages[] = [
                    'role' => 'user',
                    'content' => $msg,
                ];
            }
        }
        
        return $formattedMessages;
    }

    /**
     * Nombre de messages dans la session
     */
    public function getMessagesCountAttribute(): int
    {
        return count($this->messages ?? []);
    }

    /**
     * Vérifier si la session est expirée (pas d'activité depuis 24h)
     */
    public function getIsExpiredAttribute(): bool
    {
        if (!$this->last_activity_at) {
            return true;
        }
        
        return $this->last_activity_at->diffInHours(now()) > 24;
    }

    /**
     * Label du provider pour affichage
     */
    public function getProviderLabelAttribute(): string
    {
        $labels = [
            'mistral' => 'Mistral AI',
            'groq' => 'Groq',
        ];
        return $labels[$this->provider] ?? ucfirst($this->provider);
    }
}
