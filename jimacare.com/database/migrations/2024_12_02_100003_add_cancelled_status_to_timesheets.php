<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddCancelledStatusToTimesheets extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        if (Schema::hasTable('timesheets')) {
            // Modify the status enum to include 'cancelled'
            // Note: MySQL doesn't support ALTER ENUM directly, so we need to use raw SQL
            DB::statement("ALTER TABLE timesheets MODIFY COLUMN status ENUM('pending', 'approved', 'disputed', 'paid', 'cancelled') DEFAULT 'pending'");
            
            // Add cancellation tracking fields
            Schema::table('timesheets', function (Blueprint $table) {
                if (!Schema::hasColumn('timesheets', 'cancelled_at')) {
                    $table->timestamp('cancelled_at')->nullable()->after('approved_at');
                }
                if (!Schema::hasColumn('timesheets', 'cancellation_reason')) {
                    $table->text('cancellation_reason')->nullable()->after('cancelled_at');
                }
                if (!Schema::hasColumn('timesheets', 'cancelled_by')) {
                    $table->unsignedBigInteger('cancelled_by')->nullable()->after('cancellation_reason');
                    $table->foreign('cancelled_by')->references('id')->on('users')->onDelete('set null');
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
                if (Schema::hasColumn('timesheets', 'cancelled_by')) {
                    $table->dropForeign(['cancelled_by']);
                    $table->dropColumn('cancelled_by');
                }
                if (Schema::hasColumn('timesheets', 'cancellation_reason')) {
                    $table->dropColumn('cancellation_reason');
                }
                if (Schema::hasColumn('timesheets', 'cancelled_at')) {
                    $table->dropColumn('cancelled_at');
                }
            });
            
            // Revert status enum (remove 'cancelled')
            DB::statement("ALTER TABLE timesheets MODIFY COLUMN status ENUM('pending', 'approved', 'disputed', 'paid') DEFAULT 'pending'");
        }
    }
}

