<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('template_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('production_template_id')->constrained()->cascadeOnDelete();
            $table->string('note_type');
            $table->string('department')->nullable();
            $table->string('author')->nullable();
            $table->text('note_text');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('template_notes');
    }
};
