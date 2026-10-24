<?php

namespace App\DTOs;

use Carbon\Carbon;

final class JobData
{
    public function __construct(
        public readonly string  $externalId,
        public readonly string  $title,
        public readonly string  $organisation,
        public readonly string  $description,
        public readonly string  $location,
        public readonly ?string $postcode            = null,
        public readonly ?float  $salaryMin           = null,
        public readonly ?float  $salaryMax           = null,
        public readonly ?string $salaryText          = null,
        public readonly ?string $band                = null,
        public readonly ?string $profession          = null,
        public readonly ?string $specialty           = null,
        public readonly ?string $contractType        = null,
        public readonly ?string $employmentType      = null,
        public readonly ?string $sponsorshipInfo     = null,
        public readonly ?string $requirements        = null,
        public readonly ?string $qualifications      = null,
        public readonly ?string $experienceRequired  = null,
        public readonly ?Carbon $closingDate         = null,
        public readonly ?Carbon $publishedDate       = null,
        public readonly ?string $sourceUrl           = null,
        public readonly ?string $rawReference        = null,
        public readonly array   $rawData             = [],
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            externalId:         $data['external_id'],
            title:              $data['title'],
            organisation:       $data['organisation'],
            description:        $data['description'],
            location:           $data['location'],
            postcode:           $data['postcode'] ?? null,
            salaryMin:          isset($data['salary_min']) ? (float) $data['salary_min'] : null,
            salaryMax:          isset($data['salary_max']) ? (float) $data['salary_max'] : null,
            salaryText:         $data['salary_text'] ?? null,
            band:               $data['band'] ?? null,
            profession:         $data['profession'] ?? null,
            specialty:          $data['specialty'] ?? null,
            contractType:       $data['contract_type'] ?? null,
            employmentType:     $data['employment_type'] ?? null,
            sponsorshipInfo:    $data['sponsorship_information'] ?? null,
            requirements:       $data['requirements'] ?? null,
            qualifications:     $data['qualifications'] ?? null,
            experienceRequired: $data['experience_required'] ?? null,
            closingDate:        isset($data['closing_date']) ? Carbon::parse($data['closing_date']) : null,
            publishedDate:      isset($data['published_date']) ? Carbon::parse($data['published_date']) : null,
            sourceUrl:          $data['source_url'] ?? null,
            rawReference:       $data['raw_reference'] ?? null,
            rawData:            $data['raw_data'] ?? [],
        );
    }
}
