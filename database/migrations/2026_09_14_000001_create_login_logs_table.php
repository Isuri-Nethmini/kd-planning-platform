<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Audit trail of admin login activity.
     *
     * Records successful and failed attempts alike. Failures are the valuable
     * ones: a burst of failures against one account is what a brute-force
     * attempt looks like. admin_id is nullable because a failed attempt may use
     * an email address that does not belong to any account.
     */
    public function up(): void
    {
        Schema::create('login_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_id')->nullable()->constrained('admin_users')->nullOnDelete();
            $table->string('email');
            $table->boolean('successful')->default(false);
            $table->string('ip_address', 45)->nullable();   // 45 chars fits IPv6
            $table->string('user_agent', 512)->nullable();
            $table->timestamp('attempted_at');
            $table->timestamps();

            $table->index(['email', 'attempted_at']);
            $table->index('successful');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('login_logs');
    }
};
