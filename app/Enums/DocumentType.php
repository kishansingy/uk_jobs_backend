<?php

namespace App\Enums;

enum DocumentType: string
{
    case Cv                  = 'cv';
    case SupportingStatement = 'supporting_statement';
    case Certificate         = 'certificate';
    case Registration        = 'registration';
    case Other               = 'other';
}
