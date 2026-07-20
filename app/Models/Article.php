<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Article extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id', 'title', 'slug', 'category_id', 'writer',
        'status', 'rejection_note', 'trashed_reason', 'content', 'image', 'views', 'scheduled_at',
        'meta_title', 'meta_description', 'meta_keywords', 'canonical_url',
    ];

    // Accessors

    /** Handle external URL or local storage path */
    public function getImageUrlAttribute(): ?string
    {
        if (!$this->image) return null;
        if (str_starts_with($this->image, 'http')) return $this->image;
        return Storage::url($this->image);
    }

    /** Estimated reading time in minutes (avg 200 wpm) */
    public function getReadingTimeAttribute(): int
    {
        $wordCount = str_word_count(strip_tags($this->content ?? ''));
        return max(1, (int) ceil($wordCount / 200));
    }

    // Scopes

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    // Relationships

    public function category(): BelongsTo    { return $this->belongsTo(Category::class); }
    public function comments(): HasMany      { return $this->hasMany(Comment::class)->latest(); }
    public function user(): BelongsTo        { return $this->belongsTo(User::class); }
    public function tags(): BelongsToMany    { return $this->belongsToMany(Tag::class); }
    public function bookmarks(): HasMany     { return $this->hasMany(Bookmark::class); }
    public function readingHistories(): HasMany { return $this->hasMany(ReadingHistory::class); }
    public function revisions(): HasMany      { return $this->hasMany(\App\Models\ArticleRevision::class)->latest(); }

    // Helpers

    public function isPending(): bool       { return $this->status === 'pending'; }
    public function isPendingDelete(): bool { return $this->status === 'pending_delete'; }
    public function isActive(): bool        { return $this->status === 'active'; }
    public function isOwnedBy(User $user): bool { return $this->user_id === $user->id; }

    public function isBookmarkedBy(?User $user): bool
    {
        if (!$user) return false;
        return $this->bookmarks()->where('user_id', $user->id)->exists();
    }
}
