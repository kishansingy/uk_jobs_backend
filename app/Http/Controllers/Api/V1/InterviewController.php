<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Interview\StoreAnswerRequest;
use App\Http\Resources\InterviewSessionResource;
use App\Http\Traits\ApiResponse;
use App\Models\InterviewAnswer;
use App\Models\InterviewCategory;
use App\Models\InterviewQuestion;
use App\Models\InterviewSession;
use App\Models\Job;
use App\Services\AI\InterviewService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InterviewController extends Controller
{
    use ApiResponse;

    public function __construct(private InterviewService $service) {}

    public function categories(Request $request): JsonResponse
    {
        $categories = InterviewCategory::where('active', true)
            ->with('profession')
            ->orderBy('sort_order')
            ->get();

        return $this->success($categories->map(fn($c) => [
            'id'          => $c->id,
            'name'        => $c->name,
            'slug'        => $c->slug,
            'description' => $c->description,
            'profession'  => $c->profession?->name,
        ]));
    }

    public function questions(Request $request): JsonResponse
    {
        $query = InterviewQuestion::where('active', true)->with('category');

        if ($request->has('category_id')) {
            $query->where('category_id', $request->integer('category_id'));
        }
        if ($request->has('profession_id')) {
            $query->where(fn($q) =>
                $q->where('profession_id', $request->integer('profession_id'))
                  ->orWhereNull('profession_id')
            );
        }

        return $this->success($query->get()->map(fn($q) => [
            'id'         => $q->id,
            'question'   => $q->question,
            'guidance'   => $q->guidance,
            'difficulty' => $q->difficulty,
            'category'   => $q->category?->name,
        ]));
    }

    public function sessions(Request $request): JsonResponse
    {
        $sessions = InterviewSession::where('user_id', $request->user()->id)
            ->orderByDesc('created_at')
            ->get();

        return $this->success(InterviewSessionResource::collection($sessions));
    }

    public function startSession(Request $request): JsonResponse
    {
        $request->validate([
            'job_id' => ['nullable', 'exists:jobs,id'],
            'title'  => ['nullable', 'string', 'max:255'],
        ]);

        $session = InterviewSession::create([
            'user_id'    => $request->user()->id,
            'job_id'     => $request->job_id,
            'title'      => $request->title ?? 'Practice Session',
            'started_at' => now(),
        ]);

        // Generate job-specific questions if job provided
        if ($request->job_id) {
            $job       = Job::find($request->job_id);
            $generated = $this->service->generateQuestionsForJob($job);

            return $this->created([
                'session'    => new InterviewSessionResource($session),
                'questions'  => $generated,
                'disclaimer' => 'These questions are AI-generated for practice only. They may not reflect actual interview questions.',
            ]);
        }

        return $this->created(new InterviewSessionResource($session));
    }

    public function showSession(Request $request, InterviewSession $interviewSession): JsonResponse
    {
        $this->authorize('view', $interviewSession);

        return $this->success(new InterviewSessionResource(
            $interviewSession->load(['answers.question', 'answers.feedback'])
        ));
    }

    public function storeAnswer(StoreAnswerRequest $request, InterviewSession $interviewSession): JsonResponse
    {
        $this->authorize('update', $interviewSession);

        $audioPath = null;
        if ($request->hasFile('answer_audio')) {
            $audioPath = $request->file('answer_audio')
                ->store("interviews/{$request->user()->id}", 'private');
        }

        $answer = InterviewAnswer::create([
            'session_id'        => $interviewSession->id,
            'question_id'       => $request->question_id,
            'answer_text'       => $request->answer_text,
            'answer_audio_path' => $audioPath,
            'answered_at'       => now(),
        ]);

        return $this->created(['answer_id' => $answer->id], 'Answer saved');
    }

    public function generateFeedback(Request $request, InterviewAnswer $interviewAnswer): JsonResponse
    {
        $this->authorize('view', $interviewAnswer->session);

        if ($interviewAnswer->feedback) {
            return $this->success($interviewAnswer->feedback);
        }

        $feedback = $this->service->generateFeedback($interviewAnswer);

        return $this->created([
            'feedback'    => $feedback,
            'disclaimer'  => 'Feedback is AI-generated for practice. Not an official NHS assessment.',
        ]);
    }
}
