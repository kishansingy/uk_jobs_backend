<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Document\UploadDocumentRequest;
use App\Http\Resources\CvAnalysisResource;
use App\Http\Resources\DocumentResource;
use App\Http\Traits\ApiResponse;
use App\Models\CandidateDocument;
use App\Models\Job;
use App\Services\AI\CvAnalysisService;
use App\Services\DocumentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DocumentController extends Controller
{
    use ApiResponse;

    public function __construct(
        private DocumentService $documents,
        private CvAnalysisService $cvAnalysis
    ) {}

    public function index(Request $request): JsonResponse
    {
        $docs = CandidateDocument::where('user_id', $request->user()->id)
            ->orderByDesc('uploaded_at')
            ->get();

        return $this->success(DocumentResource::collection($docs));
    }

    public function upload(UploadDocumentRequest $request): JsonResponse
    {
        $document = $this->documents->store(
            $request->user(),
            $request->file('document'),
            $request->enum('type', \App\Enums\DocumentType::class),
            (bool) $request->boolean('is_current', true)
        );

        return $this->created(new DocumentResource($document));
    }

    public function destroy(Request $request, CandidateDocument $document): JsonResponse
    {
        $this->authorize('delete', $document);
        $this->documents->delete($document);
        return $this->noContent();
    }

    public function analyse(Request $request, CandidateDocument $document): JsonResponse
    {
        $this->authorize('view', $document);

        $job = $request->has('job_id')
            ? Job::findOrFail($request->integer('job_id'))
            : null;

        $analysis = $this->cvAnalysis->analyse($request->user(), $document, $job);

        return $this->created(new CvAnalysisResource($analysis));
    }

    public function analyses(Request $request, CandidateDocument $document): JsonResponse
    {
        $this->authorize('view', $document);

        $analyses = $document->analyses()->orderByDesc('analysed_at')->get();

        return $this->success(CvAnalysisResource::collection($analyses));
    }
}
