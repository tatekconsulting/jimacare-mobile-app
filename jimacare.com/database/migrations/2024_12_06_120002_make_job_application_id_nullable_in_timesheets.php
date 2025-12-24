<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class MakeJobApplicationIdNullableInTimesheets extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('timesheets')) {
            // Check if column exists and modify it to be nullable
            if (Schema::hasColumn('timesheets', 'job_application_id')) {
                // Use raw SQL to modify the column to be nullable
                // This is safer than trying to use Blueprint which might have issues with existing data
                DB::statement('ALTER TABLE timesheets MODIFY COLUMN job_application_id BIGINT UNSIGNED NULL');
            } else {
                // If column doesn't exist, add it as nullable
                Schema::table('timesheets', function (Blueprint $table) {
                    $table->unsignedBigInteger('job_application_id')->nullable()->after('client_id');
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Don't reverse - keeping it nullable is safer
    }
}

