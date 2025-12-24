<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCoverLetterToJobApplications extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('job_applications', function (Blueprint $table) {
            // Add cover_letter if it doesn't exist
            if (!Schema::hasColumn('job_applications', 'cover_letter')) {
                $table->text('cover_letter')->nullable()->after('carer_id');
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
            if (Schema::hasColumn('job_applications', 'cover_letter')) {
                $table->dropColumn('cover_letter');
            }
        });
    }
}

