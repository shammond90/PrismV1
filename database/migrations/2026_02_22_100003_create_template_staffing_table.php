<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('template_staffing', function (Blueprint $table) {
            $table->id();
            $table->foreignId('production_template_id')->constrained()->cascadeOnDelete();
            $table->string('department');
            $table->string('role');
            $table->unsignedInteger('quantity')->default(1);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('template_staffing');
    }
};
