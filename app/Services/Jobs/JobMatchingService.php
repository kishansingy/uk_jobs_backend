<?php

namespace App\Services\Jobs;

use App\Contracts\AiProviderInterface;
use App\DTOs\MatchResult;
use App\Models\Job;
use App\Models\JobMatch;
use App\Models\User;

class JobMatchingService
{
    public function __construct(private AiProviderInterface $ai) {}

    public function match(User $user, Job $job): JobMatch
    {
        $result = $this->calculate($user, $job);

        return JobMatch::updateOrCreate(
            ['user_id' => $user->id, 'job_id' => $job->id],
            [
                'match_score'   => $result->score,
                'match_factors' => $result->factors,
                'explanation'   => $result->explanation,
                'gaps'          => $result->gaps,
                'method'        => $result->method,
                'calculated_at' => now(),
            ]
        );
    }

    public function calculate(User $user, Job $job): MatchResult
    {
        $profile     = $user->candidateProfile;
        $preferences = $user->candidatePreferences;

        if (! $profile) {
            return new MatchResult(0, [], 'Profile incomplete — complete your profile for personalised matching.', [], 'deterministic');
        }

        $factors = [];
        $gaps    = [];
        $score   = 0;

        // Profession match (30 pts)
        if ($profile->profession_id && $job->profession_id) {
            if ($profile->profession_id === $job->profession_id) {
                $factors['profession'] = ['score' => 30, 'match' => true];
                $score += 30;
            } else {
                $factors['profession'] = ['score' => 0, 'match' => false];
                $gaps[] = 'Job is for a different profession than your profile.';
            }
        }

        // Specialty match (20 pts)
        if ($profile->specialty_id && $job->specialty_id) {
            if ($profile->specialty_id === $job->specialty_id) {
                $factors['specialty'] = ['score' => 20, 'match' => true];
                $score += 20;
            } else {
                $factors['specialty'] = ['score' => 5, 'match' => 'partial'];
                $score += 5;
                $gaps[] = 'Your specialty does not exactly match the job specialty.';
            }
        }

        // Band match (15 pts)
        if ($preferences?->preferred_band && $job->band) {
            if ($preferences->preferred_band === $job->band) {
                $factors['band'] = ['score' => 15, 'match' => true];
                $score += 15;
            } else {
                $factors['band'] = ['score' => 5, 'match' => 'partial'];
                $score += 5;
            }
        }

        // Location match (15 pts)
        if ($preferences?->preferred_locations && $job->location) {
            $locationMatch = collect($preferences->preferred_locations)
                ->contains(fn($loc) => str_contains(strtolower($job->location), strtolower($loc)));

            if ($locationMatch) {
                $factors['location'] = ['score' => 15, 'match' => true];
                $score += 15;
            } else {
                $factors['location'] = ['score' => 0, 'match' => false];
                $gaps[] = 'Job location does not match your preferred locations.';
            }
        }

        // Salary match (10 pts)
        if ($preferences?->salary_min && $job->salary_max) {
            if ($job->salary_max >= $preferences->salary_min) {
                $factors['salary'] = ['score' => 10, 'match' => true];
                $score += 10;
            } else {
                $factors['salary'] = ['score' => 0, 'match' => false];
                $gaps[] = 'Job salary may be below your minimum expectation.';
            }
        }

        // Sponsorship (10 pts)
        if ($profile->sponsorship_required) {
            $hasSponsorshipInfo = ! empty($job->sponsorship_information);
            if ($hasSponsorshipInfo) {
                $factors['sponsorship'] = ['score' => 10, 'match' => true];
                $score += 10;
            } else {
                $factors['sponsorship'] = ['score' => 0, 'match' => false];
                $gaps[] = 'No sponsorship information is available for this role. Check the official vacancy.';
            }
        }

        $explanation = $this->buildExplanation($score, $factors, $gaps, $job);

        return new MatchResult(
            score:       min(100, $score),
            factors:     $factors,
            explanation: $explanation,
            gaps:        $gaps,
            method:      'deterministic',
        );
    }

    private function buildExplanation(float $score, array $factors, array $gaps, Job $job): string
    {
        $level = match(true) {
            $score >= 80 => 'strong',
            $score >= 50 => 'good',
            $score >= 25 => 'partial',
            default      => 'low',
        };

        $base = match($level) {
            'strong'  => "This role looks like a strong match for your profile.",
            'good'    => "This role is a good match for several of your criteria.",
            'partial' => "This role partially matches your profile.",
            default   => "This role has limited overlap with your current preferences.",
        };

        $disclaimer = " This score is for guidance only and is not an official NHS assessment.";

        return $base . (count($gaps) > 0 ? " There are some areas to review." : '') . $disclaimer;
    }
}
