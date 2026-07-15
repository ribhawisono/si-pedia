<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BannedWord extends Model
{
    protected $fillable = ['word', 'created_by'];

    /**
     * True if $content contains this (or any) banned word, after
     * normalizing both sides to defeat common evasion tricks:
     *  - imbuhan/suffix menempel ("anjingnya") -> substring match, bukan
     *    \b...\b whole-word (whole-word gagal karena tidak ada boundary
     *    antara "anjing" dan "nya", semuanya huruf).
     *  - leetspeak ("4njing", "anj1ng") -> substitusi angka/simbol umum
     *    balik ke huruf sebelum dibandingkan.
     *  - huruf diulang ("aaaaanjing", "anjjjing") -> runtut huruf sama
     *    berturutan diciutkan jadi satu huruf sebelum dibandingkan.
     * Substring matching sengaja dipilih di atas whole-word supaya varian
     * berimbuhan/disisipi ikut tertangkap, dengan risiko false-positive
     * yang dianggap dapat diterima untuk kebutuhan moderasi ini.
     */
    public static function containsBannedWord(string $content): bool
    {
        $words = static::pluck('word');
        if ($words->isEmpty()) return false;

        $normalizedContent = static::normalize($content);

        foreach ($words as $word) {
            $needle = static::normalize($word);
            if ($needle !== '' && str_contains($normalizedContent, $needle)) return true;
        }

        return false;
    }

    /** Lowercase, balik leetspeak ke huruf, lalu ciutkan huruf berturutan yang sama. */
    protected static function normalize(string $text): string
    {
        $text = mb_strtolower($text, 'UTF-8');

        $leet = [
            '4' => 'a', '@' => 'a',
            '1' => 'i', '!' => 'i',
            '3' => 'e',
            '0' => 'o',
            '5' => 's', '$' => 's',
            '7' => 't',
            '8' => 'b',
        ];
        $text = strtr($text, $leet);

        // "aaaaanjing" / "anjjjing" -> "anjing"
        $text = preg_replace('/(.)\1+/u', '$1', $text);

        return $text;
    }
}
