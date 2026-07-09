<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\Lecturer;

return new class extends Migration
{
    // The previous seed (2026_07_09_000006) used url() inside a migration,
    // which runs in a console (no HTTP request) context — Laravel falls back
    // to the APP_URL config there instead of the real request host, and
    // APP_URL was still 'http://localhost'. That produced a broken URL like
    // 'http://localhost/actual-domain/images/lecturers/....jpg'.
    // Fix: store a root-relative path instead (browsers/blade resolve it
    // against the current origin automatically — no APP_URL dependency).
    public function up(): void
    {
        Lecturer::where('full_name', "Za'imatun Niswati")
            ->update(['photo' => '/images/lecturers/zaimatun-niswati.jpg']);

        Lecturer::where('full_name', 'Dwi Marlina')
            ->update(['photo' => '/images/lecturers/dwi-marlina.jpg']);
    }

    public function down(): void
    {
        Lecturer::where('full_name', "Za'imatun Niswati")->update(['photo' => null]);
        Lecturer::where('full_name', 'Dwi Marlina')->update(['photo' => null]);
    }
};
