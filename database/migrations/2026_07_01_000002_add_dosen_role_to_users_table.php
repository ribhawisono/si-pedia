<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Kompatibel MySQL & SQLite
        // MySQL: ubah enum, SQLite: alter kolom tidak support enum jadi pakai string
        try {
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'user', 'dosen') NOT NULL DEFAULT 'user'");
        } catch (\Exception $e) {
            // SQLite fallback: kolom role sudah pakai string di SQLite, tidak perlu diubah
        }
    }

    public function down(): void
    {
        try {
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'user') NOT NULL DEFAULT 'user'");
        } catch (\Exception $e) {
            // SQLite fallback
        }
    }
};
