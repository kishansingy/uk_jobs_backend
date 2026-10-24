<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Request;

class AuditService
{
    /**
     * Fields that must never be logged, even as old/new values.
     */
    private const SENSITIVE_FIELDS = [
        'password', 'remember_token', 'registration_number_encrypted',
        'api_key', 'secret', 'token', 'stripe_id',
    ];

    public function log(
        string $action,
        ?int $userId = null,
        ?Model $model = null,
        array $oldValues = [],
        array $newValues = []
    ): void {
        AuditLog::create([
            'user_id'    => $userId,
            'action'     => $action,
            'model_type' => $model ? get_class($model) : null,
            'model_id'   => $model?->getKey(),
            'old_values' => $this->sanitise($oldValues),
            'new_values' => $this->sanitise($newValues),
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);
    }

    private function sanitise(array $values): array
    {
        return array_diff_key($values, array_flip(self::SENSITIVE_FIELDS));
    }
}
