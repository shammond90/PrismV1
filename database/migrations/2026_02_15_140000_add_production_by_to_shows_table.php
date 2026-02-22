<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('shows', function (Blueprint $table) {
            if (!Schema::hasColumn('shows', 'production_by')) {
                $table->string('production_by')->nullable()->after('notes');
            }
        });
    }

    public function down()
    {
        Schema::table('shows', function (Blueprint $table) {
            if (Schema::hasColumn('shows', 'production_by')) {
                $table->dropColumn('production_by');
            }
        });
    }
};
