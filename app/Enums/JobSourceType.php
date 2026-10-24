<?php

namespace App\Enums;

enum JobSourceType: string
{
    case Api         = 'api';
    case Xml         = 'xml';
    case Json        = 'json';
    case Csv         = 'csv';
    case PartnerFeed = 'partner_feed';
    case Manual      = 'manual';
    case NhsJobs     = 'nhs_jobs';
}
