<?php

namespace App\Services\AI;

use App\Contracts\AiProviderInterface;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class GeminiProvider implements AiProviderInterface
{
    private ?string $apiKey;
    private string $model;

    public function __construct()
    {
        $this->apiKey = config('ai.gemini.api_key');
        $this->model  = config('ai.gemini.model', 'gemini-1.5-pro');
    }

    public function complete(string $prompt, array $options = []): string
    {
        if (! $this->apiKey) {
            throw new RuntimeException('Gemini API key is not configured. Set GEMINI_API_KEY in .env');
        }
        $response = $this->client()->post(
            "https://generativelanguage.googleapis.com/v1beta/models/{$this->model}:generateContent?key={$this->apiKey}",
            [
                'contents' => [
                    ['parts' => [['text' => $prompt]]],
                ],
                'generationConfig' => [
                    'temperature'     => $options['temperature'] ?? 0.3,
                    'maxOutputTokens' => $options['max_tokens'] ?? 2048,
                ],
            ]
        );

        if ($response->failed()) {
            throw new RuntimeException('Gemini API error: ' . $response->body());
        }

        return $response->json('candidates.0.content.parts.0.text') ?? '';
    }

    public function completeJson(string $prompt, array $options = []): array
    {
        $jsonPrompt = $prompt . "\n\nRespond with valid JSON only. No markdown, no explanation.";
        $text = $this->complete($jsonPrompt, $options);

        // Strip any accidental markdown fences
        $text = preg_replace('/^```json\s*/i', '', trim($text));
        $text = preg_replace('/\s*```$/', '', $text);

        $decoded = json_decode($text, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new RuntimeException('Gemini returned invalid JSON: ' . $text);
        }

        return $decoded;
    }

    public function getName(): string
    {
        return 'gemini';
    }

    private function client(): PendingRequest
    {
        return Http::timeout(30)->retry(2, 1000);
    }
}
