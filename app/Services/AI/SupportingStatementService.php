<?php

namespace App\Services\AI;

use App\Contracts\AiProviderInterface;
use App\Models\Job;
use App\Models\SupportingStatement;
use App\Models\User;

class SupportingStatementService
{
    public function __construct(private AiProviderInterface $ai) {}

    public function generate(User $user, Job $job, array $candidateInput): SupportingStatement
    {
        $profile     = $user->candidateProfile;
        $profName    = $profile?->profession?->name ?? 'healthcare professional';
        $experience  = $candidateInput['experience'] ?? 'Not provided';
        $skills      = $candidateInput['skills'] ?? 'Not provided';

        $prompt = <<<PROMPT
You are helping a UK {$profName} write a supporting statement for a job application.

Job Title: {$job->title}
Organisation: {$job->organisation}
Job Requirements: {$job->requirements}

Candidate-provided experience: {$experience}
Candidate-provided skills: {$skills}

Generate a professional supporting statement draft.

CRITICAL RULES:
- Only use information the candidate has provided above
- Do not invent any qualifications, registrations, employment history, or experience
- Flag any items that need confirmation from the candidate with [CONFIRM: ...]
- Do not guarantee employment outcomes
- Do not imply NHS endorsement
- The draft should be editable — it is a starting point, not a final document
- Write in first person
PROMPT;

        $content = $this->ai->complete($prompt);

        // Extract items requiring confirmation
        preg_match_all('/\[CONFIRM:\s*([^\]]+)\]/i', $content, $matches);
        $confirmItems = $matches[1] ?? [];

        return SupportingStatement::create([
            'user_id'                      => $user->id,
            'job_id'                       => $job->id,
            'title'                        => "Supporting Statement — {$job->title}",
            'content'                      => $content,
            'status'                       => 'draft',
            'ai_provider'                  => $this->ai->getName(),
            'items_requiring_confirmation' => $confirmItems,
        ]);
    }
}
