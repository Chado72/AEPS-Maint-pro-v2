<?php

namespace App\Http\Controllers;

use App\Models\AiSession;
use Illuminate\Http\Request;
use App\Services\AiService;

class AiChatController extends Controller
{
    protected $aiService;

    public function __construct(AiService $aiService)
    {
        $this->aiService = $aiService;
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
        $request->validate([
            'message' => 'required|string|max:2000',
            'session_id' => 'nullable|exists:ai_sessions,id',
        ]);

        $message = $request->input('message');
        $sessionId = $request->input('session_id');

        // Récupérer ou créer la session
        if ($sessionId) {
            $session = AiSession::findOrFail($sessionId);
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

        // Appeler l'IA
        $response = $this->aiService->chat($messages);

        // Mettre à jour l'historique
        $history[] = ['role' => 'user', 'content' => $message];
        $history[] = ['role' => 'assistant', 'content' => $response];
        
        $session->history = json_encode($history);
        $session->save();

        return response()->json([
            'response' => $response,
            'session_id' => $session->id,
        ]);
    }

    public function show(AiSession $session)
    {
        if ($session->user_id !== auth()->id()) {
            abort(403);
        }
        
        return view('ai.chat', compact('session'));
    }

    public function destroy(AiSession $session)
    {
        if ($session->user_id !== auth()->id()) {
            abort(403);
        }
        
        $session->delete();
        
        return redirect()->route('ai.chat.index')
            ->with('success', 'Conversation supprimée avec succès.');
    }

    public function clearHistory()
    {
        AiSession::where('user_id', auth()->id())->delete();
        
        return redirect()->route('ai.chat.index')
            ->with('success', 'Historique des conversations effacé.');
    }
}
