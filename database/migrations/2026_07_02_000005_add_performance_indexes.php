<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // articles — most queried table
        Schema::table('articles', function (Blueprint $table) {
            if (!$this->hasIndex('articles', 'articles_status_created_at_index')) {
                $table->index(['status', 'created_at'], 'articles_status_created_at_index');
            }
            if (!$this->hasIndex('articles', 'articles_status_views_index')) {
                $table->index(['status', 'views'], 'articles_status_views_index');
            }
            if (!$this->hasIndex('articles', 'articles_category_id_status_index')) {
                $table->index(['category_id', 'status'], 'articles_category_id_status_index');
            }
        });

        // users
        Schema::table('users', function (Blueprint $table) {
            if (!$this->hasIndex('users', 'users_role_index')) {
                $table->index('role', 'users_role_index');
            }
        });

        // lecturers
        Schema::table('lecturers', function (Blueprint $table) {
            if (!$this->hasIndex('lecturers', 'lecturers_status_index')) {
                $table->index('status', 'lecturers_status_index');
            }
        });

        // comments
        Schema::table('comments', function (Blueprint $table) {
            if (!$this->hasIndex('comments', 'comments_status_created_at_index')) {
                $table->index(['status', 'created_at'], 'comments_status_created_at_index');
            }
            if (!$this->hasIndex('comments', 'comments_article_id_status_index')) {
                $table->index(['article_id', 'status'], 'comments_article_id_status_index');
            }
        });

        // account_reports
        Schema::table('account_reports', function (Blueprint $table) {
            if (!$this->hasIndex('account_reports', 'account_reports_status_index')) {
                $table->index('status', 'account_reports_status_index');
            }
        });

        // reading_histories (already has index from previous migration)

        // article_revisions
        Schema::table('article_revisions', function (Blueprint $table) {
            // index already added in create migration
        });
    }

    public function down(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->dropIndexIfExists('articles_status_created_at_index');
            $table->dropIndexIfExists('articles_status_views_index');
            $table->dropIndexIfExists('articles_category_id_status_index');
        });
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndexIfExists('users_role_index');
        });
        Schema::table('lecturers', function (Blueprint $table) {
            $table->dropIndexIfExists('lecturers_status_index');
        });
        Schema::table('comments', function (Blueprint $table) {
            $table->dropIndexIfExists('comments_status_created_at_index');
            $table->dropIndexIfExists('comments_article_id_status_index');
        });
        Schema::table('account_reports', function (Blueprint $table) {
            $table->dropIndexIfExists('account_reports_status_index');
        });
    }

    private function hasIndex(string $table, string $index): bool
    {
        try {
            $indexes = \Illuminate\Support\Facades\DB::select("SHOW INDEX FROM `{$table}` WHERE Key_name = '{$index}'");
            return !empty($indexes);
        } catch (\Exception $e) {
            return false; // SQLite: just skip
        }
    }
};
