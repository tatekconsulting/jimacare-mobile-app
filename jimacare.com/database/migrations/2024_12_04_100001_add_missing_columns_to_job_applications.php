<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMissingColumnsToJobApplications extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('job_applications', function (Blueprint $table) {
            // Add rejection_reason if it doesn't exist
            if (!Schema::hasColumn('job_applications', 'rejection_reason')) {
                $table->text('rejection_reason')->nullable()->after('responded_at');
            }
            
            // Add invitation_id if it doesn't exist
            if (!Schema::hasColumn('job_applications', 'invitation_id')) {
                $table->unsignedBigInteger('invitation_id')->nullable()->after('carer_id');
            }
            
            // Add response_type if it doesn't exist
            if (!Schema::hasColumn('job_applications', 'response_type')) {
                $table->enum('response_type', ['applied', 'invited', 'direct_message'])->default('applied')->after('status');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('job_applications', function (Blueprint $table) {
            if (Schema::hasColumn('job_applications', 'rejection_reason')) {
                $table->dropColumn('rejection_reason');
            }
            if (Schema::hasColumn('job_applications', 'invitation_id')) {
                $table->dropColumn('invitation_id');
            }
            if (Schema::hasColumn('job_applications', 'response_type')) {
                $table->dropColumn('response_type');
            }
        });
    }
}

