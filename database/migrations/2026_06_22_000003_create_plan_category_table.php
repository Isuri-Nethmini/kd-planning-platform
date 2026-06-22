<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plan_category', function (Blueprint $table) {
            $table->foreignId('house_plan_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();

            $table->primary(['house_plan_id', 'category_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plan_category');
    }
};
