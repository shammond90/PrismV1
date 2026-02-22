<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('template_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('production_template_id')->constrained()->cascadeOnDelete();
            $table->string('template_name');
            $table->text('description')->nullable();
            $table->text('schedule_data')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('template_schedules');
    }
};
