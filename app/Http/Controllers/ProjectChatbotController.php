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

        $answer = $chatbot->reply(
            $validated['message'],
            $request->session()->get('chatbot_topics', []),
        );

        if ($answer === null) {
            return response()->json([
                'reply' => 'Project chat is temporarily unavailable. Please try again later or contact the barangay office.',
            ], 503);
        }

        $request->session()->put('chatbot_topics', $answer['topics']);

        return response()->json(['reply' => $answer['reply']]);
    }
}
