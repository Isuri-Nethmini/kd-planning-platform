<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The client portal.
     *
     * Added at the client's request, reversing the original requirement of no
     * customer accounts. Customers are confirmed
     * clients, never public sign-ups: an admin creates each account, usually
     * from an inquiry that reached "Converted" in the sales pipeline.
     */
    public function up(): void
    {
        // Kept separate from admin_users on purpose: a different table and a
        // different authentication guard, so a customer session can never pass
        // an admin check.
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone', 30)->nullable();
            $table->string('password');
            $table->boolean('must_change_password')->default(true);
            $table->timestamp('password_changed_at')->nullable();
            $table->timestamp('last_login_at')->nullable();
            $table->foreignId('inquiry_id')->nullable()->constrained()->nullOnDelete();
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('client_projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('house_plan_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->string('site_location')->nullable();
            $table->string('status', 20)->default('active');      // active, on_hold, completed
            $table->date('start_date')->nullable();
            $table->date('estimated_completion')->nullable();
            $table->decimal('contract_value', 12, 2)->nullable();
            $table->timestamps();
        });

        Schema::create('project_stages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_project_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->string('status', 20)->default('pending');     // pending, in_progress, completed
            $table->date('started_on')->nullable();
            $table->date('completed_on')->nullable();
            $table->text('note')->nullable();                      // visible to the client
            $table->timestamps();
        });

        // Site photos are of a client's private home, so they are stored on
        // the private disk and served only to that client and to admins.
        Schema::create('stage_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_stage_id')->constrained()->cascadeOnDelete();
            $table->string('file_path');
            $table->string('caption')->nullable();
            $table->timestamps();
        });

        // There is no payment gateway by the client's choice. Payment happens
        // offline (bank transfer); the client uploads the receipt here and an
        // admin verifies it.
        Schema::create('payment_instalments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_project_id')->constrained()->cascadeOnDelete();
            $table->string('label');                               // e.g. "Advance", "Foundation"
            $table->decimal('amount', 12, 2);
            $table->date('due_date')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('payment_receipts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_instalment_id')->constrained()->cascadeOnDelete();
            $table->string('file_path');                           // private disk
            $table->string('original_name');
            $table->decimal('amount_paid', 12, 2);
            $table->date('paid_on');
            $table->string('reference')->nullable();               // bank reference number
            $table->string('status', 20)->default('pending');     // pending, verified, rejected
            $table->foreignId('reviewed_by')->nullable()->constrained('admin_users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->string('rejection_reason')->nullable();
            $table->timestamps();

            $table->index('status');
        });

        // The Security Log now covers customer accounts too.
        Schema::table('login_logs', function (Blueprint $table) {
            $table->string('actor_type', 10)->default('admin')->after('id');   // admin, customer
        });
    }

    public function down(): void
    {
        Schema::table('login_logs', fn (Blueprint $t) => $t->dropColumn('actor_type'));
        Schema::dropIfExists('payment_receipts');
        Schema::dropIfExists('payment_instalments');
        Schema::dropIfExists('stage_photos');
        Schema::dropIfExists('project_stages');
        Schema::dropIfExists('client_projects');
        Schema::dropIfExists('customers');
    }
};
