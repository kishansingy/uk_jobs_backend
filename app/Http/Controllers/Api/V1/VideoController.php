<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\VideoResource;
use App\Http\Traits\ApiResponse;
use App\Models\Video;
use App\Models\VideoCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VideoController extends Controller
{
    use ApiResponse;

    public function index(Request $request): JsonResponse
    {
        $query = Video::published()
            ->with(['category', 'contributor', 'tags']);

        if ($request->has('category_id')) {
            $query->where('category_id', $request->integer('category_id'));
        }

        if ($request->has('tag')) {
            $query->whereHas('tags', fn($q) => $q->where('tag', $request->input('tag')));
        }

        // Gate premium videos based on subscription
        $user = $request->user();
        if (! $user || $user->activeSubscription?->plan?->slug === 'free') {
            $query->where('premium_only', false);
        }

        $videos = $query->orderByDesc('published_at')->paginate(20);

        return $this->success(
            VideoResource::collection($videos),
            'Videos retrieved',
            200,
            ['total' => $videos->total(), 'last_page' => $videos->lastPage()]
        );
    }

    public function show(Request $request, Video $video): JsonResponse
    {
        if ($video->status !== 'published') {
            return $this->error('Video not found', 404);
        }

        $this->authorize('view', $video);

        $video->increment('view_count');
        $video->load(['category', 'contributor', 'tags']);

        return $this->success(new VideoResource($video));
    }

    public function categories(): JsonResponse
    {
        $categories = VideoCategory::where('active', true)
            ->orderBy('sort_order')
            ->get(['id', 'name', 'slug', 'description']);

        return $this->success($categories);
    }
}
