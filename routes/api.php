<?php

use App\Http\Controllers\Api\V1\Admin\AdminJobController;
use App\Http\Controllers\Api\V1\Admin\JobSourceController;
use App\Http\Controllers\Api\V1\Admin\ReportController;
use App\Http\Controllers\Api\V1\Admin\UserController;
use App\Http\Controllers\Api\V1\AdvisorController;
use App\Http\Controllers\Api\V1\AlertController;
use App\Http\Controllers\Api\V1\ApplicationController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\DeviceController;
use App\Http\Controllers\Api\V1\DocumentController;
use App\Http\Controllers\Api\V1\HealthController;
use App\Http\Controllers\Api\V1\InterviewController;
use App\Http\Controllers\Api\V1\JobController;
use App\Http\Controllers\Api\V1\NotificationController;
use App\Http\Controllers\Api\V1\ProfileController;
use App\Http\Controllers\Api\V1\SubscriptionController;
use App\Http\Controllers\Api\V1\SupportingStatementController;
use App\Http\Controllers\Api\V1\VideoController;
use Illuminate\Support\Facades\Route;

// Health check — no auth required (used by Jenkins)
Route::get('/health', HealthController::class);

Route::prefix('v1')->group(function () {

    // ── Public auth ────────────────────────────────────────────────────────
    Route::prefix('auth')->group(function () {
        Route::post('register', [AuthController::class, 'register'])
            ->middleware('throttle:3,1');
        Route::post('login', [AuthController::class, 'login'])
            ->middleware('throttle:5,1');
    });

    // ── Authenticated routes ────────────────────────────────────────────────
    Route::middleware('auth:sanctum')->group(function () {

        // Auth
        Route::post('auth/logout', [AuthController::class, 'logout']);
        Route::get('auth/me', [AuthController::class, 'me']);

        // Profile
        Route::prefix('profile')->group(function () {
            Route::get('/', [ProfileController::class, 'show']);
            Route::patch('/', [ProfileController::class, 'update']);
            Route::patch('/preferences', [ProfileController::class, 'updatePreferences']);
            Route::post('/qualifications', [ProfileController::class, 'storeQualification']);
            Route::delete('/qualifications/{qualification}', [ProfileController::class, 'destroyQualification']);
        });

        // Jobs (search is public-ish — rate limited but no auth needed for listing)
        Route::prefix('jobs')->group(function () {
            Route::get('/', [JobController::class, 'index'])->withoutMiddleware('auth:sanctum');
            Route::get('/{id}', [JobController::class, 'show'])->withoutMiddleware('auth:sanctum');
            Route::post('/{id}/save', [JobController::class, 'save']);
            Route::delete('/{id}/save', [JobController::class, 'unsave']);
            Route::get('/{id}/match', [JobController::class, 'match']);
        });

        // Alerts
        Route::apiResource('alerts', AlertController::class)->except('show');
        Route::get('alerts/{alert}', [AlertController::class, 'show']);

        // Applications
        Route::apiResource('applications', ApplicationController::class);

        // Documents & CV
        Route::prefix('cv')->group(function () {
            Route::get('/', [DocumentController::class, 'index']);
            Route::post('/', [DocumentController::class, 'upload']);
            Route::delete('/{document}', [DocumentController::class, 'destroy']);
            Route::post('/{document}/analyse', [DocumentController::class, 'analyse'])
                ->middleware('subscribed:cv_analysis');
            Route::get('/{document}/analyses', [DocumentController::class, 'analyses']);
        });

        // Supporting statements
        Route::prefix('supporting-statements')->group(function () {
            Route::get('/', [SupportingStatementController::class, 'index']);
            Route::post('/generate', [SupportingStatementController::class, 'generate'])
                ->middleware('subscribed:supporting_statement');
            Route::get('/{supportingStatement}', [SupportingStatementController::class, 'show']);
            Route::patch('/{supportingStatement}', [SupportingStatementController::class, 'update']);
            Route::delete('/{supportingStatement}', [SupportingStatementController::class, 'destroy']);
        });

        // Interview preparation
        Route::prefix('interview')->group(function () {
            Route::get('/categories', [InterviewController::class, 'categories']);
            Route::get('/questions', [InterviewController::class, 'questions']);
            Route::get('/sessions', [InterviewController::class, 'sessions']);
            Route::post('/sessions', [InterviewController::class, 'startSession']);
            Route::get('/sessions/{interviewSession}', [InterviewController::class, 'showSession']);
            Route::post('/sessions/{interviewSession}/answers', [InterviewController::class, 'storeAnswer']);
            Route::post('/answers/{interviewAnswer}/feedback', [InterviewController::class, 'generateFeedback'])
                ->middleware('subscribed:interview_prep');
        });

        // Videos
        Route::get('/videos/categories', [VideoController::class, 'categories']);
        Route::get('/videos', [VideoController::class, 'index']);
        Route::get('/videos/{video}', [VideoController::class, 'show']);

        // Career advisors
        Route::prefix('advisor')->group(function () {
            Route::get('/advisors', [AdvisorController::class, 'index']);
            Route::get('/advisors/{advisor}/availability', [AdvisorController::class, 'availability']);
            Route::get('/appointments', [AdvisorController::class, 'appointments']);
            Route::post('/appointments', [AdvisorController::class, 'book'])
                ->middleware('subscribed:advisor_access');
            Route::get('/appointments/{appointment}', [AdvisorController::class, 'showAppointment']);
            Route::get('/appointments/{appointment}/messages', [AdvisorController::class, 'messages']);
            Route::post('/appointments/{appointment}/messages', [AdvisorController::class, 'sendMessage']);
        });

        // Subscriptions
        Route::prefix('subscriptions')->group(function () {
            Route::get('/plans', [SubscriptionController::class, 'plans'])->withoutMiddleware('auth:sanctum');
            Route::get('/current', [SubscriptionController::class, 'current']);
            Route::post('/', [SubscriptionController::class, 'subscribe']);
            Route::post('/cancel', [SubscriptionController::class, 'cancel']);
        });

        // Notifications
        Route::prefix('notifications')->group(function () {
            Route::get('/', [NotificationController::class, 'index']);
            Route::patch('/{notification}/read', [NotificationController::class, 'markRead']);
            Route::post('/read-all', [NotificationController::class, 'markAllRead']);
            Route::get('/preferences', [NotificationController::class, 'preferences']);
            Route::patch('/preferences', [NotificationController::class, 'updatePreferences']);
        });

        // Devices (push notification tokens)
        Route::post('/devices/register', [DeviceController::class, 'register']);
        Route::delete('/devices/{token}', [DeviceController::class, 'revoke']);

        // ── Admin routes ─────────────────────────────────────────────────────
        Route::prefix('admin')->middleware('admin')->group(function () {

            // Users
            Route::get('/users', [UserController::class, 'index']);
            Route::get('/users/{user}', [UserController::class, 'show']);
            Route::patch('/users/{user}/toggle-active', [UserController::class, 'toggleActive']);
            Route::patch('/users/{user}/role', [UserController::class, 'assignRole']);

            // Job sources
            Route::get('/sources', [JobSourceController::class, 'index']);
            Route::post('/sources', [JobSourceController::class, 'store']);
            Route::patch('/sources/{jobSource}', [JobSourceController::class, 'update']);
            Route::delete('/sources/{jobSource}', [JobSourceController::class, 'destroy']);
            Route::post('/sources/{jobSource}/import', [JobSourceController::class, 'triggerImport']);
            Route::get('/sources/{jobSource}/logs', [JobSourceController::class, 'importLogs']);
            Route::get('/sources/{jobSource}/test-scrape', [JobSourceController::class, 'testScrape']);

            // Jobs
            Route::get('/jobs', [AdminJobController::class, 'index']);
            Route::patch('/jobs/{job}', [AdminJobController::class, 'update']);
            Route::post('/jobs/{job}/reclassify', [AdminJobController::class, 'reclassify']);
            Route::delete('/jobs/{job}', [AdminJobController::class, 'destroy']);

            // Reports
            Route::get('/reports/dashboard', [ReportController::class, 'dashboard']);
            Route::get('/reports/users-by-profession', [ReportController::class, 'usersByProfession']);
            Route::get('/reports/jobs-by-profession', [ReportController::class, 'jobsByProfession']);
            Route::get('/reports/application-statuses', [ReportController::class, 'applicationStatuses']);
            Route::get('/reports/subscription-revenue', [ReportController::class, 'subscriptionRevenue']);
            Route::get('/reports/export', [ReportController::class, 'export']);
        });
    });
});
