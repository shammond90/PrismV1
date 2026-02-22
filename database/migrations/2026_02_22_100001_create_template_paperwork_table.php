<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('template_paperwork', function (Blueprint $table) {
            $table->id();
            $table->foreignId('production_template_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('department');
            $table->string('file_path');
            $table->string('original_filename');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('template_paperwork');
    }
};
