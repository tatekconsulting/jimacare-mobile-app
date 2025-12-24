<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class MakeWorkDateNullableInTimesheets extends Migration
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
            if (Schema::hasColumn('timesheets', 'work_date')) {
                // Use raw SQL to modify the column to be nullable
                DB::statement('ALTER TABLE timesheets MODIFY COLUMN work_date DATE NULL');
            } else {
                // If column doesn't exist, add it as nullable (though it seems to exist in production)
                Schema::table('timesheets', function (Blueprint $table) {
                    $table->date('work_date')->nullable()->after('date');
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

