<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // SEO fields on articles
        Schema::table('articles', function (Blueprint $table) {
            $table->string('meta_title')->nullable()->after('scheduled_at');
            $table->string('meta_description', 300)->nullable()->after('meta_title');
            $table->string('meta_keywords', 300)->nullable()->after('meta_description');
            $table->string('canonical_url')->nullable()->after('meta_keywords');
        });

        // Add 'archived' to status enum (MySQL only)
        try {
            DB::statement("ALTER TABLE articles MODIFY COLUMN status ENUM('active','draft','pending','pending_delete','archived') NOT NULL DEFAULT 'draft'");
        } catch (\Exception $e) { /* SQLite fallback */ }

        // Change comments default to pending
        try {
            DB::statement("ALTER TABLE comments MODIFY COLUMN status ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending'");
        } catch (\Exception $e) { /* SQLite fallback */ }

        // Article revisions table
        Schema::create('article_revisions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('article_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->longText('content');
            $table->string('status', 30)->default('draft');
            $table->string('revision_note', 255)->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->index(['article_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('article_revisions');
        Schema::table('articles', function (Blueprint $table) {
            $table->dropColumn(['meta_title','meta_description','meta_keywords','canonical_url']);
        });
    }
};
