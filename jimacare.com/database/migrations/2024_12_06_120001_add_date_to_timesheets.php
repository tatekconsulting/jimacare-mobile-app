<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDateToTimesheets extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('timesheets')) {
            Schema::table('timesheets', function (Blueprint $table) {
                // Add date column if it doesn't exist
                if (!Schema::hasColumn('timesheets', 'date')) {
                    $table->date('date')->nullable()->after('client_id');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasTable('timesheets')) {
            Schema::table('timesheets', function (Blueprint $table) {
                if (Schema::hasColumn('timesheets', 'date')) {
                    $table->dropColumn('date');
                }
            });
        }
    }
}

