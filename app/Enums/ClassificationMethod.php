<?php

namespace App\Enums;

enum ClassificationMethod: string
{
    case Deterministic = 'deterministic';
    case Ai            = 'ai';
    case Manual        = 'manual';
}
