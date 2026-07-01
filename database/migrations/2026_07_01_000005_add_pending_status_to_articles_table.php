<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // MySQL: ubah enum, SQLite: tidak perlu karena pakai string
        try {
            DB::statement("ALTER TABLE articles MODIFY COLUMN status ENUM('active','draft','pending','pending_delete') NOT NULL DEFAULT 'draft'");
        } catch (\Exception $e) {
            // SQLite fallback: enum tidak ada, kolom sudah pakai string
        }
    }

    public function down(): void
    {
        try {
            DB::statement("ALTER TABLE articles MODIFY COLUMN status ENUM('active','draft') NOT NULL DEFAULT 'draft'");
        } catch (\Exception $e) {
            // SQLite fallback
        }
    }
};
