<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContractsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contracts', function (Blueprint $table) {
            $table->id();
	        $table->foreignId('user_id')->nullable();
	        $table->foreignId('role_id')->nullable();
            $table->string('title');
	        $table->string('slug')->nullable();

	        $table->string('company')->nullable();
			$table->enum('gender', ['male', 'female'])->nullable();

	        $table->enum('start_type', ['immediately', 'not-sure', 'specific-date'])->default('immediately');
	        $table->date('start_date')->nullable();

	        $table->enum('end_type', ['on-going', 'fixed-period'])->default('on-going');
	        $table->date('end_date')->nullable();

	        $table->time('start_time')->nullable();
	        $table->time('end_time')->nullable();

	        $table->unsignedInteger('hourly_rate')->nullable();
	        $table->unsignedInteger('daily_rate')->nullable();
	        $table->unsignedInteger('weekly_rate')->nullable();


			$table->text('desc')->nullable();

	        $table->boolean('drive')->nullable();

	        $table->enum('how_often', [ 'Daily', 'Twice a week', 'Weekly', 'Every other week', 'Once a month', 'One time clean', 'Other' ])->nullable();
	        $table->enum('beds', [ '0 bedrooms', '1 bedroom', '2 bedrooms', '3 bedrooms', '4 bedrooms', '5+ bedrooms', 'Studio' ])->nullable();
	        $table->enum('baths', [ '1 bathroom', '1 bathroom + 1 additional toilet', '2 bathrooms', '2 bathrooms + 1 additional toilet', '3 bathrooms', '4+ bathrooms' ])->nullable();
	        $table->enum('rooms', [ '0', '1', '2', '3', '4+' ])->nullable();
	        $table->enum('cleaning_type', [ 'Standard cleaning', 'Deep cleaning', 'Move-out cleaning' ])->nullable();

	        $table->enum('status', ['pending', 'active'])->default('pending');
            $table->softDeletes();
            $table->timestamps();
        });

	    Schema::create('contract_type', function (Blueprint $table) {
		    $table->foreignId('contract_id');//->constrained();
		    $table->foreignId('type_id');//->constrained();
	    });
        Schema::create('contract_day', function (Blueprint $table) {
		    $table->foreignId('contract_id');//->constrained();
		    $table->foreignId('day_id');//->constrained();
	    });
	    Schema::create('contract_language', function (Blueprint $table) {
		    $table->foreignId('contract_id');//->constrained();
		    $table->foreignId('language_id');//->constrained();
	    });
	    Schema::create('contract_interest', function (Blueprint $table) {
		    $table->foreignId('contract_id');//->constrained();
		    $table->foreignId('interest_id');//->constrained();
	    });
	    Schema::create('contract_education', function (Blueprint $table) {
		    $table->foreignId('contract_id');//->constrained();
		    $table->foreignId('education_id');//->constrained();
	    });
	    Schema::create('contract_experience', function (Blueprint $table) {
		    $table->foreignId('contract_id');//->constrained();
		    $table->foreignId('experience_id');//->constrained();
	    });
	    Schema::create('contract_skill', function (Blueprint $table) {
		    $table->foreignId('contract_id');//->constrained();
		    $table->foreignId('skill_id');//->constrained();
	    });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
	    Schema::dropIfExists('contract_type');
	    Schema::dropIfExists('contract_day');
	    Schema::dropIfExists('contract_language');
	    Schema::dropIfExists('contract_interest');
	    Schema::dropIfExists('contract_education');
	    Schema::dropIfExists('contract_experience');
	    Schema::dropIfExists('contract_skill');
	    Schema::dropIfExists('contracts');
    }
}
