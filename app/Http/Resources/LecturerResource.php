<?php
namespace App\Http\Resources;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LecturerResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'nidn'           => $this->nidn,
            'address'        => $this->address,
            'photo'          => $this->photo
                ? (str_starts_with($this->photo, 'http') ? $this->photo : \Illuminate\Support\Facades\Storage::url($this->photo))
                : null,
            'status'         => $this->status,
            'name'           => $this->user?->name,
            'email'          => $this->user?->email,
        ];
    }
}
