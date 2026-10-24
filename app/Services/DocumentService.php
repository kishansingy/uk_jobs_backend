<?php

namespace App\Services;

use App\Enums\DocumentType;
use App\Models\CandidateDocument;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DocumentService
{
    private const ALLOWED_MIMES = ['application/pdf', 'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];

    public function store(User $user, UploadedFile $file, DocumentType $type, bool $isCurrent = false): CandidateDocument
    {
        // Double-check MIME at service layer (defence in depth beyond FormRequest)
        if (! in_array($file->getMimeType(), self::ALLOWED_MIMES)) {
            throw new \InvalidArgumentException('Invalid file type.');
        }

        $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $path     = "documents/{$user->id}/{$filename}";

        Storage::disk('private')->putFileAs("documents/{$user->id}", $file, $filename);

        if ($isCurrent) {
            // Unset previous current document of same type
            CandidateDocument::where('user_id', $user->id)
                ->where('type', $type->value)
                ->where('is_current', true)
                ->update(['is_current' => false]);
        }

        return CandidateDocument::create([
            'user_id'           => $user->id,
            'type'              => $type->value,
            'original_filename' => $file->getClientOriginalName(),
            'storage_path'      => $path,
            'disk'              => 'private',
            'mime_type'         => $file->getMimeType(),
            'file_size'         => $file->getSize(),
            'is_current'        => $isCurrent,
            'uploaded_at'       => now(),
        ]);
    }

    /**
     * Generate a short-lived pre-signed URL for secure download.
     */
    public function temporaryUrl(CandidateDocument $document, int $minutes = 5): string
    {
        return Storage::disk($document->disk)->temporaryUrl($document->storage_path, now()->addMinutes($minutes));
    }

    public function delete(CandidateDocument $document): void
    {
        Storage::disk($document->disk)->delete($document->storage_path);
        $document->delete();
    }
}
