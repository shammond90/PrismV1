<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('productions', function (Blueprint $table) {
            $table->foreignId('space_id')->nullable()->constrained('spaces')->onDelete('set null');
            $table->date('initial_contact_date')->nullable();
            $table->foreignId('primary_company_id')->nullable()->constrained('companies')->onDelete('set null');
            $table->foreignId('primary_contact_id')->nullable()->constrained('contacts')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('productions', function (Blueprint $table) {
            $table->dropForeign(['space_id']);
            $table->dropColumn('space_id');
            $table->dropColumn('initial_contact_date');
            $table->dropForeign(['primary_company_id']);
            $table->dropColumn('primary_company_id');
            $table->dropForeign(['primary_contact_id']);
            $table->dropColumn('primary_contact_id');
        });
    }
};
