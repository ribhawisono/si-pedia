<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Article extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id', 'title', 'slug', 'category_id', 'writer',
        'status', 'content', 'image', 'views', 'scheduled_at',
    ];

    // Accessor: handle both external URLs and local storage paths
    public function getImageUrlAttribute(): ?string
    {
        if (!$this->image) return null;
        if (str_starts_with($this->image, 'http')) return $this->image;
        return Storage::url($this->image);
    }

    public function category(): BelongsTo { return $this->belongsTo(Category::class); }
    public function comments(): HasMany   { return $this->hasMany(Comment::class)->latest(); }
    public function user(): BelongsTo    { return $this->belongsTo(User::class); }

    public function isPending(): bool       { return $this->status === 'pending'; }
    public function isPendingDelete(): bool { return $this->status === 'pending_delete'; }
    public function isActive(): bool        { return $this->status === 'active'; }
    public function isOwnedBy(User $user): bool { return $this->user_id === $user->id; }
}
