<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        Schema::table('shows', function (Blueprint $table) {
            if (!Schema::hasColumn('shows', 'choreography_by')) {
                $table->string('choreography_by')->nullable()->after('notes');
            }
        });

        // Copy existing data from production_by to choreography_by
        DB::statement('UPDATE `shows` SET `choreography_by` = `production_by` WHERE `production_by` IS NOT NULL');

        // Drop old column if present
        Schema::table('shows', function (Blueprint $table) {
            if (Schema::hasColumn('shows', 'production_by')) {
                $table->dropColumn('production_by');
            }
        });
    }

    public function down()
    {
        // Add back production_by and copy data back
        Schema::table('shows', function (Blueprint $table) {
            if (!Schema::hasColumn('shows', 'production_by')) {
                $table->string('production_by')->nullable()->after('notes');
            }
        });

        DB::statement('UPDATE `shows` SET `production_by` = `choreography_by` WHERE `choreography_by` IS NOT NULL');

        Schema::table('shows', function (Blueprint $table) {
            if (Schema::hasColumn('shows', 'choreography_by')) {
                $table->dropColumn('choreography_by');
            }
        });
    }
};
