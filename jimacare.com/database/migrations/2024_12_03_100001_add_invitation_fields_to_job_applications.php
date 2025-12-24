<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddInvitationFieldsToJobApplications extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('job_applications', function (Blueprint $table) {
            if (!Schema::hasColumn('job_applications', 'invitation_id')) {
                $table->unsignedBigInteger('invitation_id')->nullable()->after('carer_id');
                $table->foreign('invitation_id')->references('id')->on('job_invitations')->onDelete('set null');
            }
            if (!Schema::hasColumn('job_applications', 'response_type')) {
                $table->enum('response_type', ['applied', 'invited', 'direct_message'])->default('applied')->after('status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('job_applications', function (Blueprint $table) {
            $table->dropForeign(['invitation_id']);
            $table->dropColumn(['invitation_id', 'response_type']);
        });
    }
}

