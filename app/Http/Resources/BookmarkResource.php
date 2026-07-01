<?php
namespace App\Http\Resources;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookmarkResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'article_id' => $this->article_id,
            'created_at' => $this->created_at?->toISOString(),
            'article'    => new ArticleResource($this->whenLoaded('article')),
        ];
    }
}
