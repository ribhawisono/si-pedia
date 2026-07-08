<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            // Distinguishes *why* an article ended up in Trash:
            // 'deleted'  = a normal hapus (admin destroy, or user request-delete
            //              approved by admin) — stays hidden from the writer,
            //              only admin can restore / permanently delete.
            // 'takedown' = admin pulled a live article down but wants the writer
            //              to fix it — still lands in Trash for admin, but ALSO
            //              surfaces in the writer's "Artikel Saya" so they can
            //              edit and resubmit it.
            $table->enum('trashed_reason', ['deleted', 'takedown'])->nullable()->after('rejection_note');
        });
    }

    public function down(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->dropColumn('trashed_reason');
        });
    }
};
