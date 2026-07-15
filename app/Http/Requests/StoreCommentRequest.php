<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCommentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Batas kata normal untuk komentar ensiklopedia: minimal 3 kata
            // (biar bukan cuma "oke"/emoji doang) dan maksimal 150 kata
            // (biar tetap ringkas, bukan esai). Ini terpisah dari filter kata
            // terlarang (BannedWord) yang menahan komentar untuk ditinjau
            // admin — batas ini langsung menolak submit-nya di awal.
            'content' => ['required', 'string', 'max:1000', function ($attribute, $value, $fail) {
                $wordCount = str_word_count(strip_tags($value));
                if ($wordCount < 3) {
                    $fail('Komentar terlalu pendek, minimal 3 kata.');
                } elseif ($wordCount > 150) {
                    $fail('Komentar terlalu panjang, maksimal 150 kata.');
                }
            }],
        ];
    }
}
