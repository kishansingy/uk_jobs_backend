<?php

namespace App\Providers;

use App\Models\Application;
use App\Models\Appointment;
use App\Models\CandidateDocument;
use App\Models\InterviewSession;
use App\Models\JobAlert;
use App\Models\JobSource;
use App\Models\SupportingStatement;
use App\Models\Video;
use App\Policies\ApplicationPolicy;
use App\Policies\AppointmentPolicy;
use App\Policies\CandidateDocumentPolicy;
use App\Policies\InterviewSessionPolicy;
use App\Policies\JobAlertPolicy;
use App\Policies\JobSourcePolicy;
use App\Policies\SupportingStatementPolicy;
use App\Policies\VideoPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Application::class        => ApplicationPolicy::class,
        Appointment::class        => AppointmentPolicy::class,
        CandidateDocument::class  => CandidateDocumentPolicy::class,
        InterviewSession::class   => InterviewSessionPolicy::class,
        JobAlert::class           => JobAlertPolicy::class,
        JobSource::class          => JobSourcePolicy::class,
        SupportingStatement::class=> SupportingStatementPolicy::class,
        Video::class              => VideoPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();
    }
}
