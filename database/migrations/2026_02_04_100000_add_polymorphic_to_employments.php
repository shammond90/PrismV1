<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Make company_id nullable
        $driver = DB::getDriverName();
        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE `employments` MODIFY `company_id` BIGINT UNSIGNED NULL;');
        }

        // Drop foreign key constraint first if it exists
        Schema::table('employments', function (Blueprint $table) {
            try {
                $table->dropForeign(['company_id']);
            } catch (\Throwable $e) {
                // ignore if doesn't exist
            }
        });

        // Add polymorphic columns
        Schema::table('employments', function (Blueprint $table) {
            if (!Schema::hasColumn('employments', 'employable_type')) {
                $table->string('employable_type')->nullable()->after('company_id');
            }
            if (!Schema::hasColumn('employments', 'employable_id')) {
                $table->unsignedBigInteger('employable_id')->nullable()->after('employable_type');
            }
            $table->index(['employable_type', 'employable_id'], 'employments_employable_index');
        });

        // Migrate existing company_id data to employable columns
        DB::table('employments')
            ->whereNotNull('company_id')
            ->whereNull('employable_type')
            ->update([
                'employable_type' => 'App\\Models\\Company',
                'employable_id' => DB::raw('company_id'),
            ]);
    }

    public function down(): void
    {
        Schema::table('employments', function (Blueprint $table) {
            try {
                $table->dropIndex('employments_employable_index');
            } catch (\Throwable $e) {
                // ignore
            }
            if (Schema::hasColumn('employments', 'employable_type')) {
                $table->dropColumn('employable_type');
            }
            if (Schema::hasColumn('employments', 'employable_id')) {
                $table->dropColumn('employable_id');
            }
        });
    }
};
