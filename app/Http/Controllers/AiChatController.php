<?php

namespace App\Http\Controllers;

use App\Models\AiSession;
use Illuminate\Http\Request;
use App\Services\AiService;
use App\Services\AuditService;

class AiChatController extends Controller
{
    protected $aiService;
    protected $auditService;
    
    // Limite de requêtes par minute
    private const RATE_LIMIT = 10;

    public function __construct(AiService $aiService, AuditService $auditService)
    {
        $this->aiService = $aiService;
        $this->auditService = $auditService;
        $this->middleware('auth');
    }

    public function index()
    {
        $sessions = AiSession::where('user_id', auth()->id())
            ->orderByDesc('updated_at')
            ->limit(10)
            ->get();
        
        return view('ai.chat', compact('sessions'));
    }

    public function chat(Request $request)
    {
        // Rate limiting simple basé sur la session
        $rateLimitKey = 'ai_rate_limit_' . auth()->id();
        $attempts = session($rateLimitKey, 0);
        $lastAttempt = session($rateLimitKey . '_time', 0);
        
        // Réinitialiser le compteur après 1 minute
        if (time() - $lastAttempt > 60) {
            $attempts = 0;
        }
        
        if ($attempts >= self::RATE_LIMIT) {
            return response()->json([
                'error' => 'Trop de requêtes. Veuillez attendre une minute.',
            ], 429);
        }
        
        // Incrémenter le compteur
        session([$rateLimitKey => $attempts + 1, $rateLimitKey . '_time' => time()]);

        $validated = $request->validate([
            'message' => 'required|string|max:2000',
            'session_id' => 'nullable|exists:ai_sessions,id',
        ]);

        $message = $validated['message'];
        $sessionId = $validated['session_id'] ?? null;

        // Récupérer ou créer la session
        if ($sessionId) {
            $session = AiSession::findOrFail($sessionId);
            
            // Vérifier que l'utilisateur possède la session
            if ($session->user_id !== auth()->id() && !auth()->user()->isAdmin()) {
                return response()->json(['error' => 'Accès non autorisé à cette session.'], 403);
            }
        } else {
            $session = AiSession::create([
                'user_id' => auth()->id(),
                'title' => substr($message, 0, 50) . '...',
            ]);
        }

        // Construire l'historique de conversation
        $messages = [
            ['role' => 'system', 'content' => 'Tu es un assistant expert en maintenance des systèmes d\'eau potable (AEPS/PEA) au Burkina Faso. Tu aides les techniciens et gestionnaires à diagnostiquer des pannes, recommander des pièces détachées, et fournir des conseils de maintenance préventive.'],
        ];

        // Ajouter l'historique de la session
        $history = json_decode($session->history, true) ?? [];
        foreach ($history as $msg) {
            $messages[] = $msg;
        }

        // Ajouter le message actuel
        $messages[] = ['role' => 'user', 'content' => $message];

        try {
            // Appeler l'IA
            $response = $this->aiService->chat($messages);

            // Mettre à jour l'historique
            $history[] = ['role' => 'user', 'content' => $message];
            $history[] = ['role' => 'assistant', 'content' => $response];
            
            $session->history = json_encode($history);
            $session->save();

            // Log l'utilisation de l'IA
            $this->auditService->log('ai_chat', 'AiSession', $session->id, [
                'message_length' => strlen($message),
                'response_length' => strlen($response),
            ]);

            return response()->json([
                'response' => $response,
                'session_id' => $session->id,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erreur lors de la communication avec l\'IA: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function show(AiSession $session)
    {
        // Middleware OwnsAiSession gère déjà la vérification
        // Cette méthode peut être utilisée pour afficher une session spécifique
        
        return view('ai.chat', compact('session'));
    }

    public function destroy(AiSession $session)
    {
        // Middleware OwnsAiSession gère déjà la vérification
        
        $session->delete();
        
        $this->auditService->log('delete', 'AiSession', $session->id);
        
        return redirect()->route('ai.chat.index')
            ->with('success', 'Conversation supprimée avec succès.');
    }

    public function clearHistory()
    {
        $count = AiSession::where('user_id', auth()->id())->count();
        AiSession::where('user_id', auth()->id())->delete();
        
        $this->auditService->log('clear_history', 'AiSession', null, [
            'sessions_deleted' => $count,
        ]);
        
        return redirect()->route('ai.chat.index')
            ->with('success', 'Historique des conversations effacé.');
    }
}
