<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('show_catalogues', function (Blueprint $table) {
            if (!Schema::hasColumn('show_catalogues', 'choreography_by')) {
                $table->string('choreography_by')->nullable()->after('description');
            }
        });
    }

    public function down()
    {
        Schema::table('show_catalogues', function (Blueprint $table) {
            if (Schema::hasColumn('show_catalogues', 'choreography_by')) {
                $table->dropColumn('choreography_by');
            }
        });
    }
};
