<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('contact_production', function (Blueprint $table) {
            $table->string('role')->nullable()->after('contact_id');
            $table->string('department')->nullable()->after('role');
            $table->text('notes')->nullable()->after('department');
        });
    }

    public function down()
    {
        Schema::table('contact_production', function (Blueprint $table) {
            $table->dropColumn(['role','department','notes']);
        });
    }
};
