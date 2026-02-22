<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('shows', function (Blueprint $table) {
            $table->foreignId('show_catalogue_id')->nullable()->constrained('show_catalogues')->nullOnDelete();
        });
    }

    public function down()
    {
        Schema::table('shows', function (Blueprint $table) {
            $table->dropConstrainedForeignId('show_catalogue_id');
        });
    }
};
