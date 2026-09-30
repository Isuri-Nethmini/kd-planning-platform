<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Turns the login log into a single security log.
     *
     * Blocked page access used to be written only to laravel.log, which the
     * owner cannot read. Recording it here puts every security event — logins,
     * lockouts and blocked access — on one page in the admin panel.
     *
     *   event = 'login'          a sign-in attempt; `successful` says how it went
     *   event = 'lockout'        a sign-in refused because the account was locked
     *   event = 'access_denied'  a logged-in staff admin tried a restricted page
     */
    public function up(): void
    {
        Schema::table('login_logs', function (Blueprint $table) {
            $table->string('event', 20)->default('login')->after('email');
            $table->string('path')->nullable()->after('event');   // the page that was refused
            $table->index('event');
        });
    }

    public function down(): void
    {
        Schema::table('login_logs', function (Blueprint $table) {
            $table->dropIndex(['event']);
            $table->dropColumn(['event', 'path']);
        });
    }
};
