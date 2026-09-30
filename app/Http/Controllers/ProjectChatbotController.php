<?php

namespace App\Http\Controllers;

use App\Http\ProjectChatbot;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProjectChatbotController extends Controller
{
    public function __invoke(Request $request, ProjectChatbot $chatbot): JsonResponse
    {
        $validated = $request->validate([
            'message' => ['required', 'string', 'min:2', 'max:500'],
        ]);

        $history = $request->session()->get('chatbot_history', []);
        $answer = $chatbot->reply($validated['message'], is_array($history) ? $history : []);

        if ($answer === null) {
            return response()->json([
                'reply' => 'Project chat is temporarily unavailable. Please try again later or contact the barangay office.',
            ], 503);
        }

        $request->session()->put('chatbot_history', $answer['history']);

        return response()->json(['reply' => $answer['reply']]);
    }
}
