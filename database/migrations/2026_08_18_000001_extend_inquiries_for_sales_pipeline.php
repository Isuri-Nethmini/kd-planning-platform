<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Extends inquiries from a simple read/unread flag into a small sales
     * pipeline, so the owner can track which inquiries actually became sales
     * rather than only which ones he has opened.
     *
     * `status` moves from an ENUM to a plain string. Adding a value to a MySQL
     * ENUM means rewriting the column definition every time the pipeline
     * changes; a string plus application-level validation (see the Inquiry
     * model) is easier to extend and behaves identically on SQLite.
     */
    public function up(): void
    {
        Schema::table('inquiries', function (Blueprint $table) {
            $table->string('status')->default('new')->change();
        });

        Schema::table('inquiries', function (Blueprint $table) {
            $table->text('admin_notes')->nullable()->after('message');
            $table->decimal('quoted_amount', 12, 2)->nullable()->after('admin_notes');
            $table->timestamp('responded_at')->nullable()->after('quoted_amount');
        });
    }

    public function down(): void
    {
        Schema::table('inquiries', function (Blueprint $table) {
            $table->dropColumn(['admin_notes', 'quoted_amount', 'responded_at']);
        });

        Schema::table('inquiries', function (Blueprint $table) {
            $table->enum('status', ['new', 'read', 'responded'])->default('new')->change();
        });
    }
};
