<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BannedWord extends Model
{
    protected $fillable = ['word', 'created_by'];

    /**
     * True if $content contains this (or any) banned word as a whole word
     * (case-insensitive, punctuation-agnostic boundaries) — not merely a
     * substring, so a banned word like "kontol" won't false-positive on an
     * unrelated word that happens to contain it as a fragment.
     */
    public static function containsBannedWord(string $content): bool
    {
        $words = static::pluck('word');
        if ($words->isEmpty()) return false;

        foreach ($words as $word) {
            $pattern = '/\b' . preg_quote($word, '/') . '\b/iu';
            if (preg_match($pattern, $content)) return true;
        }

        return false;
    }
}
