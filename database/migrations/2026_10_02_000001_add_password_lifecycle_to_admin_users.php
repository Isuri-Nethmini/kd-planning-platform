<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Temporary passwords with a forced change on first sign-in.
     *
     * Previously the primary admin typed a new staff member's password and it
     * was emailed in plain text, where it stayed valid indefinitely. Now the
     * system generates a random temporary password, flags the account, and the
     * holder cannot use the admin panel until they set their own — after which
     * the emailed password no longer works.
     */
    public function up(): void
    {
        Schema::table('admin_users', function (Blueprint $table) {
            $table->boolean('must_change_password')->default(false)->after('role');
            $table->timestamp('password_changed_at')->nullable()->after('must_change_password');
        });

        Schema::table('login_logs', function (Blueprint $table) {
            // A free-text note on a security event, e.g. who reset a password.
            $table->string('note')->nullable()->after('path');

            // The account holder's name at the time of the event. admin_id is
            // set to null when an account is deleted, so without this snapshot
            // a deleted account's history would become anonymous — exactly the
            // history you would want to investigate.
            $table->string('admin_name')->nullable()->after('admin_id');
        });
    }

    public function down(): void
    {
        Schema::table('admin_users', function (Blueprint $table) {
            $table->dropColumn(['must_change_password', 'password_changed_at']);
        });

        Schema::table('login_logs', function (Blueprint $table) {
            $table->dropColumn(['note', 'admin_name']);
        });
    }
};
