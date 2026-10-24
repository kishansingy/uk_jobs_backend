<?php

namespace App\Services\AI;

use App\Contracts\AiProviderInterface;
use App\Models\InterviewAnswer;
use App\Models\InterviewFeedback;
use App\Models\InterviewQuestion;
use App\Models\Job;
use App\Models\User;

class InterviewService
{
    public function __construct(private AiProviderInterface $ai) {}

    public function generateFeedback(InterviewAnswer $answer): InterviewFeedback
    {
        $question = $answer->question;

        $prompt = <<<PROMPT
You are reviewing a practice interview answer for a UK healthcare role.

Interview Question: {$question->question}
Candidate's Answer: {$answer->answer_text}

Provide feedback as JSON with:
- structure_feedback: array of observations about answer structure (e.g. STAR method usage)
- relevance_feedback: array of observations about relevance to the question
- clarity_feedback: array of observations about communication clarity
- overall_feedback: string summary with constructive suggestions

IMPORTANT:
- This is practice feedback only, not an official NHS or employer assessment
- Be constructive and specific
- Do not score the answer as pass/fail
- Do not claim to predict interview outcomes
PROMPT;

        $result = $this->ai->completeJson($prompt);

        return InterviewFeedback::create([
            'answer_id'          => $answer->id,
            'structure_feedback' => $result['structure_feedback'] ?? [],
            'relevance_feedback' => $result['relevance_feedback'] ?? [],
            'clarity_feedback'   => $result['clarity_feedback'] ?? [],
            'overall_feedback'   => $result['overall_feedback'] ?? '',
            'ai_provider'        => $this->ai->getName(),
            'generated_at'       => now(),
        ]);
    }

    public function generateQuestionsForJob(Job $job, int $count = 5): array
    {
        $profession = $job->profession?->name ?? 'healthcare professional';

        $prompt = <<<PROMPT
Generate {$count} interview questions for a UK NHS {$profession} role.

Job Title: {$job->title}
Organisation: {$job->organisation}

Return JSON array of objects with:
- question: string
- category: string (general_nhs | profession_specific | clinical_scenario | communication | teamwork | safeguarding | leadership)
- difficulty: string (easy | medium | hard)

Focus on realistic NHS interview questions. Do not invent clinical protocols.
PROMPT;

        return $this->ai->completeJson($prompt);
    }
}
