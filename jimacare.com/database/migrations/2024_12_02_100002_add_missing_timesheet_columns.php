<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMissingTimesheetColumns extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        if (Schema::hasTable('timesheets')) {
            Schema::table('timesheets', function (Blueprint $table) {
                // Add missing columns if they don't exist
                if (!Schema::hasColumn('timesheets', 'total_amount')) {
                    $table->decimal('total_amount', 10, 2)->nullable()->after('hourly_rate');
                }
                
                if (!Schema::hasColumn('timesheets', 'approved_at')) {
                    $table->timestamp('approved_at')->nullable()->after('status');
                }
                
                if (!Schema::hasColumn('timesheets', 'dispute_reason')) {
                    $table->text('dispute_reason')->nullable()->after('approved_at');
                }
                
                if (!Schema::hasColumn('timesheets', 'location_lat')) {
                    $table->decimal('location_lat', 10, 8)->nullable()->after('notes');
                }
                
                if (!Schema::hasColumn('timesheets', 'location_lng')) {
                    $table->decimal('location_lng', 11, 8)->nullable()->after('location_lat');
                }
                
                if (!Schema::hasColumn('timesheets', 'client_id')) {
                    $table->unsignedBigInteger('client_id')->after('carer_id');
                    $table->foreign('client_id')->references('id')->on('users')->onDelete('cascade');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        // Don't remove columns in case they're needed
    }
}

