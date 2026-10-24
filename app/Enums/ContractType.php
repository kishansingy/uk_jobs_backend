<?php

namespace App\Enums;

enum ContractType: string
{
    case Permanent  = 'permanent';
    case FixedTerm  = 'fixed_term';
    case Temporary  = 'temporary';
    case Bank       = 'bank';
    case Locum      = 'locum';
    case Agency     = 'agency';
    case Volunteer  = 'volunteer';
    case Secondment = 'secondment';
    case Other      = 'other';
}
