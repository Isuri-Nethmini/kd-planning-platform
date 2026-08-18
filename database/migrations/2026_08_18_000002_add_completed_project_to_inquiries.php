<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Lets an inquiry reference a completed project instead of (or as well as)
     * a catalogue plan.
     *
     * The client asked for this: buyers often see a house KD has already built
     * and want "that one, with a few changes" rather than something from the
     * catalogue. Without this the request arrived as free text and the office
     * had to guess which house was meant.
     */
    public function up(): void
    {
        Schema::table('inquiries', function (Blueprint $table) {
            $table->foreignId('completed_project_id')
                ->nullable()
                ->after('house_plan_id')
                ->constrained('completed_projects')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('inquiries', function (Blueprint $table) {
            $table->dropConstrainedForeignId('completed_project_id');
        });
    }
};
