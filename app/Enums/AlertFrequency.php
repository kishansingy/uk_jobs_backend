<?php

namespace App\Enums;

enum AlertFrequency: string
{
    case Instant = 'instant';
    case Daily   = 'daily';
    case Weekly  = 'weekly';
}
