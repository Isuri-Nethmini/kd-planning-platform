<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Requirement fields for general inquiries.
     *
     * A buyer who has not picked a catalogue plan can now tell the office what
     * they want (budget, bedrooms, bathrooms, floors, land size) instead of
     * typing it into the message. All optional, so plan inquiries are unchanged.
     */
    public function up(): void
    {
        Schema::table('inquiries', function (Blueprint $table) {
            $table->string('budget', 20)->nullable()->after('message');
            $table->unsignedTinyInteger('bedrooms')->nullable()->after('budget');
            $table->unsignedTinyInteger('bathrooms')->nullable()->after('bedrooms');
            $table->unsignedTinyInteger('floors')->nullable()->after('bathrooms');
            $table->string('land_size', 20)->nullable()->after('floors');
        });
    }

    public function down(): void
    {
        Schema::table('inquiries', function (Blueprint $table) {
            $table->dropColumn(['budget', 'bedrooms', 'bathrooms', 'floors', 'land_size']);
        });
    }
};
