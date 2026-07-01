<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ArticleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'               => $this->id,
            'title'            => $this->title,
            'slug'             => $this->slug,
            'content'          => $this->when($request->routeIs('api.articles.show'), $this->content),
            'excerpt'          => \Illuminate\Support\Str::limit(strip_tags($this->content), 200),
            'image'            => $this->image_url,
            'views'            => $this->views,
            'reading_time'     => $this->reading_time,
            'status'           => $this->status,
            'writer'           => $this->writer,
            'meta_title'       => $this->meta_title,
            'meta_description' => $this->meta_description,
            'created_at'       => $this->created_at?->toISOString(),
            'updated_at'       => $this->updated_at?->toISOString(),
            'category'         => new CategoryResource($this->whenLoaded('category')),
            'author'           => new UserResource($this->whenLoaded('user')),
            'tags'             => TagResource::collection($this->whenLoaded('tags')),
            'comments_count'   => $this->whenCounted('comments'),
            'bookmarks_count'  => $this->whenCounted('bookmarks'),
            'is_bookmarked'    => $this->when(
                $request->user(),
                fn () => $this->bookmarks()->where('user_id', $request->user()?->id)->exists()
            ),
            'url' => route('articles.show', $this->slug),
        ];
    }
}
