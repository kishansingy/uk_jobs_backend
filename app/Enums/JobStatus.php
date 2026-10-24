<?php

namespace App\Enums;

enum JobStatus: string
{
    case Draft = 'draft';
    case Active = 'active';
    case Updated = 'updated';
    case ClosingSoon = 'closing_soon';
    case Closed = 'closed';
    case Expired = 'expired';
    case Removed = 'removed';

    public function label(): string
    {
        return match($this) {
            self::Draft       => 'Draft',
            self::Active      => 'Active',
            self::Updated     => 'Updated',
            self::ClosingSoon => 'Closing Soon',
            self::Closed      => 'Closed',
            self::Expired     => 'Expired',
            self::Removed     => 'Removed',
        };
    }

    public function isVisible(): bool
    {
        return in_array($this, [self::Active, self::Updated, self::ClosingSoon]);
    }
}
