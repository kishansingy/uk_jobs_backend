<?php

namespace App\Services\AI;

use App\Contracts\AiProviderInterface;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class OpenAiProvider implements AiProviderInterface
{
    private string $apiKey;
    private string $model;

    public function __construct()
    {
        $this->apiKey = config('ai.openai.api_key');
        $this->model  = config('ai.openai.model', 'gpt-4o');
    }

    public function complete(string $prompt, array $options = []): string
    {
        $response = Http::timeout(30)
            ->retry(2, 1000)
            ->withToken($this->apiKey)
            ->post('https://api.openai.com/v1/chat/completions', [
                'model'       => $this->model,
                'messages'    => [['role' => 'user', 'content' => $prompt]],
                'temperature' => $options['temperature'] ?? 0.3,
                'max_tokens'  => $options['max_tokens'] ?? 2048,
            ]);

        if ($response->failed()) {
            throw new RuntimeException('OpenAI API error: ' . $response->body());
        }

        return $response->json('choices.0.message.content') ?? '';
    }

    public function completeJson(string $prompt, array $options = []): array
    {
        $response = Http::timeout(30)
            ->retry(2, 1000)
            ->withToken($this->apiKey)
            ->post('https://api.openai.com/v1/chat/completions', [
                'model'           => $this->model,
                'messages'        => [['role' => 'user', 'content' => $prompt]],
                'temperature'     => $options['temperature'] ?? 0.3,
                'max_tokens'      => $options['max_tokens'] ?? 2048,
                'response_format' => ['type' => 'json_object'],
            ]);

        if ($response->failed()) {
            throw new RuntimeException('OpenAI API error: ' . $response->body());
        }

        $text = $response->json('choices.0.message.content') ?? '{}';
        $decoded = json_decode($text, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new RuntimeException('OpenAI returned invalid JSON');
        }

        return $decoded;
    }

    public function getName(): string
    {
        return 'openai';
    }
}
