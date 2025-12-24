<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('timesheets', function (Blueprint $table) {
            // Face verification for clock in
            $table->boolean('clock_in_verified')->default(false)->after('clock_in');
            $table->string('clock_in_verification_photo')->nullable()->after('clock_in_verified');
            $table->decimal('clock_in_verification_confidence', 5, 2)->nullable()->after('clock_in_verification_photo');
            $table->timestamp('clock_in_verified_at')->nullable()->after('clock_in_verification_confidence');
            
            // Face verification for clock out
            $table->boolean('clock_out_verified')->default(false)->after('clock_out');
            $table->string('clock_out_verification_photo')->nullable()->after('clock_out_verified');
            $table->decimal('clock_out_verification_confidence', 5, 2)->nullable()->after('clock_out_verification_photo');
            $table->timestamp('clock_out_verified_at')->nullable()->after('clock_out_verification_confidence');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('timesheets', function (Blueprint $table) {
            $table->dropColumn([
                'clock_in_verified',
                'clock_in_verification_photo',
                'clock_in_verification_confidence',
                'clock_in_verified_at',
                'clock_out_verified',
                'clock_out_verification_photo',
                'clock_out_verification_confidence',
                'clock_out_verified_at',
            ]);
        });
    }
};

