<?php
namespace App\Http\Resources;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'content'    => $this->content,
            'status'     => $this->status,
            'created_at' => $this->created_at?->toISOString(),
            'author'     => new UserResource($this->whenLoaded('user')),
        ];
    }
}
