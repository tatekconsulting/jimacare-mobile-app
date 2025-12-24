<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPaidAtToTimesheets extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        if (Schema::hasTable('timesheets')) {
            Schema::table('timesheets', function (Blueprint $table) {
                if (!Schema::hasColumn('timesheets', 'paid_at')) {
                    $table->timestamp('paid_at')->nullable()->after('approved_at');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        if (Schema::hasTable('timesheets')) {
            Schema::table('timesheets', function (Blueprint $table) {
                if (Schema::hasColumn('timesheets', 'paid_at')) {
                    $table->dropColumn('paid_at');
                }
            });
        }
    }
}

