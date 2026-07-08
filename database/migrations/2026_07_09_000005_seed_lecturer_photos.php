<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Storage;
use App\Models\Lecturer;

return new class extends Migration
{
    public function up(): void
    {
        // NOTE: base64 image payloads intentionally omitted here due to size;
        // see chat attachment. Run `php artisan db:seed --class=LecturerPhotoSeeder`
        // or manually place files at storage/app/public/lecturers/ and update
        // the Lecturer rows below with the correct 'photo' path.
        Lecturer::updateOrCreate(
            ['full_name' => 'Dwi Marlina'],
            [
                'photo'         => 'lecturers/dwi-marlina.png',
                'study_program' => 'Sistem Informasi',
                'expertise'     => 'Sekretaris Program Studi',
                'status'        => 'active',
            ]
        );

        Lecturer::updateOrCreate(
            ['full_name' => "Za'imatun Niswati"],
            [
                'photo'         => 'lecturers/zaimatun-niswati.png',
                'study_program' => 'Sistem Informasi',
                'expertise'     => 'Ketua Program Studi',
                'status'        => 'active',
            ]
        );
    }

    public function down(): void
    {
        Lecturer::where('full_name', 'Dwi Marlina')->delete();
        Lecturer::where('full_name', "Za'imatun Niswati")->delete();
    }
};
