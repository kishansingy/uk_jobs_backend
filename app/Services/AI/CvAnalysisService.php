<?php

namespace App\Services\AI;

use App\Contracts\AiProviderInterface;
use App\Models\CandidateDocument;
use App\Models\CvAnalysis;
use App\Models\Job;
use App\Models\User;

class CvAnalysisService
{
    public function __construct(private AiProviderInterface $ai) {}

    public function analyse(User $user, CandidateDocument $document, ?Job $job = null): CvAnalysis
    {
        // In production, extract text from the PDF/DOC before sending to AI.
        // We send metadata here; actual text extraction requires a library like smalot/pdfparser.
        $jobContext = $job
            ? "Job Title: {$job->title}\nOrganisation: {$job->organisation}\nJob Requirements: {$job->requirements}"
            : 'No specific job — provide general feedback.';

        $prompt = <<<PROMPT
You are a UK healthcare career advisor reviewing a CV.

Candidate CV filename: {$document->original_filename}
{$jobContext}

Provide structured feedback as JSON with these keys:
- structure_feedback: array of observations about CV layout and structure
- experience_feedback: array of observations about work experience presented
- skills_feedback: array of observations about skills evidenced
- missing_evidence: array of items that should be added or clarified
- recommendations: array of specific actionable improvements

IMPORTANT RULES:
- Only comment on what is evident from the CV
- Never invent qualifications, employment history, or registration status
- Never guarantee employment outcomes
- If something cannot be assessed without reading the document, say so
- Do not claim NHS endorsement
PROMPT;

        $result = $this->ai->completeJson($prompt);

        return CvAnalysis::create([
            'user_id'             => $user->id,
            'document_id'         => $document->id,
            'job_id'              => $job?->id,
            'structure_feedback'  => $result['structure_feedback'] ?? [],
            'experience_feedback' => $result['experience_feedback'] ?? [],
            'skills_feedback'     => $result['skills_feedback'] ?? [],
            'missing_evidence'    => $result['missing_evidence'] ?? [],
            'recommendations'     => $result['recommendations'] ?? [],
            'ai_provider'         => $this->ai->getName(),
            'analysed_at'         => now(),
        ]);
    }
}
