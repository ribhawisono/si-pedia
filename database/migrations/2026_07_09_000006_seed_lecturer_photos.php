<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\Lecturer;

return new class extends Migration
{
    // Sets the photo URL for the two lecturers to the static routes defined
    // in routes/lecturer_photos.php (photo itself is served via base64,
    // mirroring the favicon pattern — no file needs to exist on disk).
    public function up(): void
    {
        Lecturer::updateOrCreate(
            ['full_name' => "Za'imatun Niswati"],
            [
                'photo'         => url('/images/lecturers/zaimatun-niswati.jpg'),
                'study_program' => 'Sistem Informasi',
                'expertise'     => 'Ketua Program Studi Sistem Informasi',
                'status'        => 'active',
            ]
        );

        Lecturer::updateOrCreate(
            ['full_name' => 'Dwi Marlina'],
            [
                'photo'         => url('/images/lecturers/dwi-marlina.jpg'),
                'study_program' => 'Sistem Informasi',
                'expertise'     => 'Sekretaris Program Studi Sistem Informasi',
                'status'        => 'active',
            ]
        );
    }

    public function down(): void
    {
        Lecturer::where('full_name', "Za'imatun Niswati")->update(['photo' => null]);
        Lecturer::where('full_name', 'Dwi Marlina')->update(['photo' => null]);
    }
};
