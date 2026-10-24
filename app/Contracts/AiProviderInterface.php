<?php

namespace App\Contracts;

interface AiProviderInterface
{
    /**
     * Send a prompt and get a text response.
     */
    public function complete(string $prompt, array $options = []): string;

    /**
     * Send a structured prompt and get a JSON-decoded array.
     */
    public function completeJson(string $prompt, array $options = []): array;

    /**
     * Provider identifier (e.g. 'gemini', 'openai').
     */
    public function getName(): string;
}
