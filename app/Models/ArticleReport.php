<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArticleReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'reporter_id', 'article_id', 'reason', 'description', 'status', 'admin_note',
    ];

    public function reporter()
    {
        return $this->belongsTo(User::class, 'reporter_id');
    }

    public function article()
    {
        return $this->belongsTo(Article::class);
    }
}
