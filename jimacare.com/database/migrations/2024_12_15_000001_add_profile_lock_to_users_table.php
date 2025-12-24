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
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('profile_locked')->default(false)->after('profile');
            $table->timestamp('profile_verified_at')->nullable()->after('profile_locked');
            $table->string('profile_verification_id')->nullable()->after('profile_verified_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['profile_locked', 'profile_verified_at', 'profile_verification_id']);
        });
    }
};

