<?php

namespace App\DTOs;

final class MatchResult
{
    public function __construct(
        public readonly float  $score,
        public readonly array  $factors,
        public readonly string $explanation,
        public readonly array  $gaps,
        public readonly string $method, // 'deterministic' | 'ai'
    ) {}
}
