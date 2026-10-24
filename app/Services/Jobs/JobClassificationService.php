<?php

namespace App\Services\Jobs;

use App\Contracts\AiProviderInterface;
use App\Enums\ClassificationMethod;
use App\Models\Job;
use App\Models\Profession;
use App\Models\Specialty;
use Illuminate\Support\Str;

class JobClassificationService
{
    private float $confidenceThreshold;

    public function __construct(private AiProviderInterface $ai)
    {
        $this->confidenceThreshold = (float) config('ai.classification_confidence_threshold', 0.75);
    }

    public function classify(Job $job): void
    {
        // 1. Try deterministic rules first
        $result = $this->classifyDeterministic($job);

        // 2. Fall back to AI if confidence is too low
        if ($result['confidence'] < $this->confidenceThreshold) {
            $result = $this->classifyWithAi($job);
        }

        $job->update([
            'profession_id'             => $result['profession_id'],
            'specialty_id'              => $result['specialty_id'],
            'band'                      => $result['band'] ?? $job->band,
            'classification_method'     => $result['method'],
            'classification_confidence' => $result['confidence'],
        ]);
    }

    private function classifyDeterministic(Job $job): array
    {
        $text = strtolower($job->title . ' ' . $job->description);

        $professions = Profession::where('active', true)->get();

        foreach ($professions as $profession) {
            $slug = $profession->slug;

            $keywords = $this->getProfessionKeywords($slug);
            $matched  = 0;
            $total    = count($keywords);

            foreach ($keywords as $keyword) {
                if (str_contains($text, $keyword)) {
                    $matched++;
                }
            }

            if ($total > 0 && ($matched / $total) >= 0.3) {
                $confidence = min(0.95, ($matched / $total) + 0.4);
                $specialty  = $this->detectSpecialty($text, $profession->id);

                return [
                    'profession_id' => $profession->id,
                    'specialty_id'  => $specialty?->id,
                    'band'          => $this->extractBand($text),
                    'method'        => ClassificationMethod::Deterministic->value,
                    'confidence'    => $confidence,
                ];
            }
        }

        return [
            'profession_id' => null,
            'specialty_id'  => null,
            'band'          => $this->extractBand($text),
            'method'        => ClassificationMethod::Deterministic->value,
            'confidence'    => 0.0,
        ];
    }

    private function classifyWithAi(Job $job): array
    {
        $professionNames = Profession::where('active', true)->pluck('name')->join(', ');

        $prompt = <<<PROMPT
You are classifying a UK healthcare job vacancy.

Job Title: {$job->title}
Organisation: {$job->organisation}
Description (first 1000 chars): {$job->description}

Available professions: {$professionNames}

Respond with JSON containing:
- profession: string (exact profession name from list, or null)
- specialty: string (medical specialty if identifiable, or null)
- band: string (NHS AfC band number like "5", "6", "7", or null)
- confidence: float (0.0 to 1.0)

Base decisions only on the job content. Do not invent information.
PROMPT;

        try {
            $result     = $this->ai->completeJson($prompt);
            $profession = Profession::where('name', $result['profession'] ?? '')->first();
            $specialty  = null;

            if ($profession && ! empty($result['specialty'])) {
                $specialty = Specialty::where('profession_id', $profession->id)
                    ->where('name', 'like', '%' . ($result['specialty']) . '%')
                    ->first();
            }

            return [
                'profession_id' => $profession?->id,
                'specialty_id'  => $specialty?->id,
                'band'          => $result['band'] ?? null,
                'method'        => ClassificationMethod::Ai->value,
                'confidence'    => (float) ($result['confidence'] ?? 0.5),
            ];
        } catch (\Throwable) {
            return [
                'profession_id' => null,
                'specialty_id'  => null,
                'band'          => null,
                'method'        => ClassificationMethod::Ai->value,
                'confidence'    => 0.0,
            ];
        }
    }

    private function detectSpecialty(string $text, int $professionId): ?Specialty
    {
        return Specialty::where('profession_id', $professionId)
            ->get()
            ->first(fn($s) => str_contains($text, strtolower($s->name)));
    }

    private function extractBand(string $text): ?string
    {
        if (preg_match('/\bband\s*([2-9])\b/i', $text, $m)) {
            return $m[1];
        }
        if (preg_match('/\bafc\s*band\s*([2-9])\b/i', $text, $m)) {
            return $m[1];
        }
        return null;
    }

    private function getProfessionKeywords(string $slug): array
    {
        return match($slug) {
            'doctor'                         => ['doctor', 'physician', 'gp', 'registrar', 'consultant', 'medical officer', 'junior doctor'],
            'nurse'                          => ['nurse', 'nursing', 'rnm', 'rn', 'staff nurse', 'ward nurse'],
            'pharmacist'                     => ['pharmacist', 'pharmacy', 'dispensing', 'clinical pharmacist'],
            'physiotherapist'                => ['physiotherapist', 'physiotherapy', 'physio'],
            'radiographer'                   => ['radiographer', 'radiography', 'imaging', 'radiology'],
            'occupational-therapist'         => ['occupational therapist', 'occupational therapy', 'ot'],
            'speech-and-language-therapist'  => ['speech and language', 'speech therapist', 'salt', 'slt'],
            'healthcare-assistant'           => ['healthcare assistant', 'hca', 'clinical support worker', 'care assistant'],
            'midwife'                        => ['midwife', 'midwifery', 'midwives'],
            'biomedical-scientist'           => ['biomedical scientist', 'biomedical science', 'laboratory', 'pathology'],
            default                          => [str_replace('-', ' ', $slug)],
        };
    }
}
