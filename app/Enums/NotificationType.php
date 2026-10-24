<?php

namespace App\Enums;

enum NotificationType: string
{
    case NewMatchingJob       = 'new_matching_job';
    case JobClosingSoon       = 'job_closing_soon';
    case JobUpdated           = 'job_updated';
    case InterviewReminder    = 'interview_reminder';
    case AppointmentReminder  = 'appointment_reminder';
    case SubscriptionExpiry   = 'subscription_expiry';
    case NewContent           = 'new_content';
}
