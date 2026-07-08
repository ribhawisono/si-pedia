<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // `mahasiswas` table + Mahasiswa model were created early on but never
    // actually used anywhere: no controller creates/reads rows, no seeder
    // populates it, no view renders student-specific data — the User model's
    // `role` column (user/dosen/admin) already covers this distinction.
    public function up(): void
    {
        Schema::dropIfExists('mahasiswas');
    }

    public function down(): void
    {
        Schema::create('mahasiswas', function ($table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('nim')->nullable();
            $table->string('study_program')->nullable();
            $table->string('force')->nullable();
            $table->timestamps();
        });
    }
};
