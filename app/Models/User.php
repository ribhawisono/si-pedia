<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name', 'username', 'email', 'password', 'role',
        'study_program', 'force', 'avatar',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    // ─── Accessors ────────────────────────────────────────────────────────────

    public function getAvatarUrlAttribute(): string
    {
        if ($this->avatar) {
            if (str_starts_with($this->avatar, 'http')) return $this->avatar;
            return asset('storage/' . $this->avatar);
        }
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=336cbc&color=fff&size=128';
    }

    // ─── Relationships ────────────────────────────────────────────────────────

    public function lecturer()        { return $this->hasOne(Lecturer::class); }
    public function mahasiswa()       { return $this->hasOne(Mahasiswa::class); }
    public function articles()        { return $this->hasMany(Article::class); }
    public function bookmarks()       { return $this->hasMany(Bookmark::class); }
    public function readingHistories(){ return $this->hasMany(ReadingHistory::class)->latest('read_at'); }

    public function bookmarkedArticles()
    {
        return $this->belongsToMany(Article::class, 'bookmarks')->withTimestamps()->latest('bookmarks.created_at');
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

    public function isDosen(): bool { return $this->role === 'dosen'; }
    public function isAdmin(): bool { return $this->role === 'admin'; }
}
