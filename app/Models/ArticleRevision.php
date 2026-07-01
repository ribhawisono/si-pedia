<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArticleRevision extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'article_id', 'user_id', 'title', 'content', 'status', 'revision_note',
    ];

    protected $casts = ['created_at' => 'datetime'];

    public function article() { return $this->belongsTo(Article::class); }
    public function user()    { return $this->belongsTo(User::class); }
}
