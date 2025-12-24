<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFilledFieldsToContracts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('contracts', function (Blueprint $table) {
            if (!Schema::hasColumn('contracts', 'filled_at')) {
                $table->timestamp('filled_at')->nullable()->after('status');
            }
            if (!Schema::hasColumn('contracts', 'filled_by_application_id')) {
                $table->unsignedBigInteger('filled_by_application_id')->nullable()->after('filled_at');
            }
            if (!Schema::hasColumn('contracts', 'reposted_at')) {
                $table->timestamp('reposted_at')->nullable()->after('filled_by_application_id');
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
        Schema::table('contracts', function (Blueprint $table) {
            if (Schema::hasColumn('contracts', 'filled_at')) {
                $table->dropColumn('filled_at');
            }
            if (Schema::hasColumn('contracts', 'filled_by_application_id')) {
                $table->dropColumn('filled_by_application_id');
            }
            if (Schema::hasColumn('contracts', 'reposted_at')) {
                $table->dropColumn('reposted_at');
            }
        });
    }
}

