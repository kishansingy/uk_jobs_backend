<?php

use App\Jobs\CloseExpiredJobs;
use App\Jobs\FetchJobsFromSource;
use App\Jobs\SendJobAlertNotifications;
use App\Models\CandidateProfile;
use App\Models\JobSource;
use Illuminate\Support\Facades\Schedule;

// Every 5 minutes: only fetch sources whose profession has registered users.
// Also respects each source's own fetch_interval_minutes to avoid over-fetching.
Schedule::call(function () {
    $now = now();

    // Get profession IDs that have at least one registered user profile
    $activeProfessionIds = CandidateProfile::whereNotNull('profession_id')
        ->distinct()
        ->pluck('profession_id')
        ->toArray();

    if (empty($activeProfessionIds)) {
        return; // No registered users yet — nothing to fetch
    }

    JobSource::where('active', true)
        ->where('authorised', true)
        ->where('fetch_method', 'pull')
        ->where(function ($q) use ($activeProfessionIds) {
            // Include sources that match a registered profession,
            // OR sources with no profession_id (generic/catch-all sources)
            $q->whereIn('profession_id', $activeProfessionIds)
              ->orWhereNull('profession_id');
        })
        ->get()
        ->each(function (JobSource $source) use ($now) {
            $interval = $source->fetch_interval_minutes ?? 60;

            // Skip if imported more recently than its interval
            if ($source->last_success_at && $source->last_success_at->diffInMinutes($now) < $interval) {
                return;
            }

            FetchJobsFromSource::dispatch($source);
        });
})->everyFiveMinutes()->name('fetch-active-profession-sources')->withoutOverlapping();

// Mark closing_soon and expired jobs daily at 1am
Schedule::job(new CloseExpiredJobs())->dailyAt('01:00')->name('close-expired-jobs');

// Daily alert notifications at 7am
Schedule::job(new SendJobAlertNotifications('daily'))->dailyAt('07:00')->name('daily-alerts');

// Weekly alert notifications Monday 7am
Schedule::job(new SendJobAlertNotifications('weekly'))->weeklyOn(1, '07:00')->name('weekly-alerts');

// Instant alerts — check every 15 minutes
Schedule::job(new SendJobAlertNotifications('instant'))->everyFifteenMinutes()->name('instant-alerts');
