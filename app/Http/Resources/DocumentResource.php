<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DocumentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                => $this->id,
            'type'              => $this->type?->value,
            'original_filename' => $this->original_filename,
            'mime_type'         => $this->mime_type,
            'file_size'         => $this->file_size,
            'is_current'        => $this->is_current,
            'uploaded_at'       => $this->uploaded_at?->toISOString(),
            // Never expose storage_path in public responses
        ];
    }
}
