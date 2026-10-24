<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VideoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'               => $this->id,
            'title'            => $this->title,
            'description'      => $this->description,
            'video_url'        => $this->video_url,
            'thumbnail_url'    => $this->thumbnail_url,
            'duration_seconds' => $this->duration_seconds,
            'premium_only'     => $this->premium_only,
            'view_count'       => $this->view_count,
            'published_at'     => $this->published_at?->toISOString(),
            'category'         => $this->whenLoaded('category', fn() => [
                'id'   => $this->category->id,
                'name' => $this->category->name,
                'slug' => $this->category->slug,
            ]),
            'contributor'      => $this->whenLoaded('contributor', fn() => [
                'name'       => $this->contributor->name,
                'profession' => $this->contributor->profession,
                'role'       => $this->contributor->role,
                // organisation only shown — no endorsement implied
                'organisation' => $this->contributor->organisation,
            ]),
            'tags'             => $this->whenLoaded('tags', fn() =>
                $this->tags->pluck('tag')
            ),
        ];
    }
}
