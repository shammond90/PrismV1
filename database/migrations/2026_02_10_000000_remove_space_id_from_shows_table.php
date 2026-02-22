<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('shows', function (Blueprint $table) {
            if (Schema::hasColumn('shows', 'space_id')) {
                // drop foreign if exists
                try {
                    $table->dropForeign(['space_id']);
                } catch (\Exception $e) {
                    // ignore if constraint name differs or doesn't exist
                }

                $table->dropColumn('space_id');
            }
        });
    }

    public function down()
    {
        Schema::table('shows', function (Blueprint $table) {
            $table->foreignId('space_id')->nullable()->constrained('spaces')->onDelete('cascade');
        });
    }
};
