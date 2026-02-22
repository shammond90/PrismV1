<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('contact_production', function (Blueprint $table) {
            if (!Schema::hasColumn('contact_production', 'departments')) {
                $table->json('departments')->nullable()->after('department');
            }
            if (!Schema::hasColumn('contact_production', 'positions')) {
                $table->json('positions')->nullable()->after('departments');
            }
        });
    }

    public function down()
    {
        Schema::table('contact_production', function (Blueprint $table) {
            if (Schema::hasColumn('contact_production', 'positions')) {
                $table->dropColumn('positions');
            }
            if (Schema::hasColumn('contact_production', 'departments')) {
                $table->dropColumn('departments');
            }
        });
    }
};
