<?php

namespace App\Enums;

enum ApplicationStatus: string
{
    case Interested  = 'interested';
    case Saved       = 'saved';
    case Preparing   = 'preparing';
    case Applied     = 'applied';
    case Shortlisted = 'shortlisted';
    case Interview   = 'interview';
    case Offer       = 'offer';
    case Rejected    = 'rejected';
    case Withdrawn   = 'withdrawn';
    case Joined      = 'joined';

    public function label(): string
    {
        return match($this) {
            self::Interested  => 'Interested',
            self::Saved       => 'Saved',
            self::Preparing   => 'Preparing',
            self::Applied     => 'Applied',
            self::Shortlisted => 'Shortlisted',
            self::Interview   => 'Interview Scheduled',
            self::Offer       => 'Offer Received',
            self::Rejected    => 'Rejected',
            self::Withdrawn   => 'Withdrawn',
            self::Joined      => 'Joined',
        };
    }
}
