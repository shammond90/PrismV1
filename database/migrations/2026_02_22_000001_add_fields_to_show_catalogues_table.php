<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('show_catalogues', function (Blueprint $table) {
            $table->text('rights_licensing')->nullable()->after('description');
            $table->json('tags')->nullable()->after('rights_licensing');
            $table->string('thumbnail')->nullable()->after('tags');
        });
    }

    public function down(): void
    {
        Schema::table('show_catalogues', function (Blueprint $table) {
            $table->dropColumn(['rights_licensing', 'tags', 'thumbnail']);
        });
    }
};
