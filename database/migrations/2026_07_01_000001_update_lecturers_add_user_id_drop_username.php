<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lecturers', function (Blueprint $table) {
            // Tambah user_id sebagai foreign key ke tabel users (relasi one-to-one)
            $table->foreignId('user_id')->nullable()->unique()->constrained('users')->nullOnDelete()->after('id');
        });

        Schema::table('lecturers', function (Blueprint $table) {
            // Hapus kolom username dari tabel lecturers
            $table->dropColumn('username');
        });
    }

    public function down(): void
    {
        Schema::table('lecturers', function (Blueprint $table) {
            $table->string('username')->nullable()->after('nip');
        });

        Schema::table('lecturers', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });
    }
};
