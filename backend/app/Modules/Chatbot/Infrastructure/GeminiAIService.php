<?php

namespace App\Modules\Chatbot\Infrastructure;

use App\Modules\Chatbot\Domain\AIServiceInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiAIService implements AIServiceInterface
{
    public function reply(string $userMessage, array $history, array $contextChunks): string
    {
        $context = implode("\n---\n", $contextChunks);
        $local = new LocalAssistantService;
        $fallback = $local->reply($userMessage, $contextChunks);

        $apiKey = config('services.gemini.key');
        if (! filled($apiKey)) {
            return $fallback;
        }

        $contents = [];
        foreach (array_slice($history, -8) as $message) {
            $contents[] = [
                'role' => $message['role'] === 'assistant' ? 'model' : 'user',
                'parts' => [['text' => $message['content']]],
            ];
        }
        $contents[] = [
            'role' => 'user',
            'parts' => [['text' => $userMessage]],
        ];

        $system = <<<PROMPT
Você é a Mia, assistente virtual da Smarty Hardware (loja de peças de computador e assistência técnica em São Paulo).
Responda em português, tom amigável e objetivo, em 2 a 8 frases.
Use APENAS o contexto abaixo para produtos, estoque, preços, frete, garantia e pagamentos.
Formas de pagamento: PIX, cartão de crédito, cartão de débito, boleto, dinheiro no balcão e transferência — todas disponíveis no checkout (ambiente sandbox).
Horário: seg–sex 9h–18h, sáb 9h–13h. Frete de peças R$ 15; serviços sem frete.
Se a informação não estiver no contexto, diga que não encontrou e ofereça falar com a bancada.

Contexto:
{$context}
PROMPT;

        try {
            $model = config('services.gemini.model', 'gemini-2.0-flash');
            $response = Http::timeout(20)
                ->withQueryParameters(['key' => $apiKey])
                ->post("https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent", [
                    'system_instruction' => ['parts' => [['text' => $system]]],
                    'contents' => $contents,
                ]);

            if (! $response->successful()) {
                Log::warning('Gemini error', ['status' => $response->status(), 'body' => $response->body()]);

                return $fallback;
            }

            $text = $response->json('candidates.0.content.parts.0.text');

            return is_string($text) && $text !== '' ? $text : $fallback;
        } catch (\Throwable $e) {
            Log::warning('Gemini exception', ['message' => $e->getMessage()]);

            return $fallback;
        }
    }
}
