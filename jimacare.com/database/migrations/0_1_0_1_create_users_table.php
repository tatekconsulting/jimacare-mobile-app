<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
	        $table->foreignId('role_id')->default(2);

            $table->string('profile')->nullable();
	        $table->string('firstname')->nullable();
	        $table->string('lastname')->nullable();
	        $table->string('email')->unique();
	        $table->string('phone')->nullable()->unique();
	        $table->date('dob')->nullable();
	        $table->enum('gender', ['male', 'female'])->nullable();

	        $table->string('country')->nullable();//->constrained();
	        //$table->string('state')->nullable();
	        $table->string('city')->nullable();
	        $table->text('address')->nullable();
	        $table->string('postcode')->nullable();
	        $table->double('lat', 16, 10)->nullable();
	        $table->double('long',  16, 10)->nullable();
	        $table->unsignedInteger('years_experience')->nullable();

	        $table->unsignedInteger('fee')->nullable();
	        $table->unsignedInteger('service_charges')->nullable();

	        // References
	        $table->string('referee1_name')->nullable();
	        $table->string('referee1_email')->nullable();
	        $table->string('referee1_phone')->nullable();
	        $table->foreignId('referee1_country_id')->nullable();
	        $table->string('referee1_child_age')->nullable();
	        $table->string('referee1_how_long')->nullable();
	        $table->string('referee1_how_contact')->nullable();

	        $table->string('referee2_name')->nullable();
	        $table->string('referee2_email')->nullable();
	        $table->string('referee2_phone')->nullable();
	        $table->foreignId('referee2_country_id')->nullable();
	        $table->string('referee2_child_age')->nullable();
	        $table->string('referee2_how_long')->nullable();
	        $table->string('referee2_how_contact')->nullable();

	        // DBS
	        $table->boolean('dbs')->nullable();
	        $table->enum('dbs_type', ['basic', 'standard', 'ehnanced'])->nullable();
	        $table->date('dbs_issue')->nullable();
	        $table->string('dbs_cert')->nullable();

	        $table->text('info')->nullable();
	        $table->text('other')->nullable();

	        $table->enum('status', ['pending', 'review', 'active', 'block'])->default('pending');
	        $table->string('password');

	        $table->timestamp('email_verified_at')->nullable();
	        $table->timestamp('phone_verified_at')->nullable();
			$table->timestamp('profile_completed_at')->nullable();
			$table->timestamp('last_login')->nullable();

            $table->rememberToken();
            $table->timestamps();
        });

	    Schema::create('language_user', function (Blueprint $table) {
	    	$table->foreignId('user_id');//->constrained();
		    $table->foreignId('language_id');//->constrained();
	    });

	    Schema::create('type_user', function (Blueprint $table) {
		    $table->foreignId('user_id');//->constrained();
		    $table->foreignId('type_id');//->constrained();
	    });

	    Schema::create('day_user', function (Blueprint $table) {
		    $table->foreignId('user_id');//->constrained();
		    $table->foreignId('day_id');//->constrained();
	    });

	    Schema::create('experience_user', function (Blueprint $table) {
		    $table->foreignId('user_id');//->constrained();
		    $table->foreignId('experience_id');//->constrained();
	    });

	    Schema::create('education_user', function (Blueprint $table) {
		    $table->foreignId('user_id');//->constrained();
		    $table->foreignId('education_id');//->constrained();
	    });

	    Schema::create('skill_user', function (Blueprint $table) {
		    $table->foreignId('user_id');//->constrained();
		    $table->foreignId('skill_id');//->constrained();
	    });

	    Schema::create('interest_user', function (Blueprint $table) {
		    $table->foreignId('user_id');//->constrained();
		    $table->foreignId('interest_id');//->constrained();
	    });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
	    Schema::dropIfExists('language_user');
	    Schema::dropIfExists('type_user');
	    Schema::dropIfExists('interest_user');
	    Schema::dropIfExists('experience_user');
	    Schema::dropIfExists('skill_user');
    }
}
