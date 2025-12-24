<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReferencesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('references', function (Blueprint $table) {
            $table->id();
			$table->foreignId('user_id');
			$table->integer('type');
			$table->string('first_name')->nullable();
			$table->string('last_name')->nullable();
			$table->string('email')->nullable();
			$table->string('job_title')->nullable();
			$table->string('organisation')->nullable();
			$table->date('from')->nullable();
			$table->date('to')->nullable();
			$table->string('emp_job_title')->nullable();
			$table->string('emp_key_duty')->nullable();
			$table->string('comment')->nullable();
			$table->boolean('emp_currently_work')->default(false);
			$table->boolean('emp_safety_issue')->default(false);
			$table->boolean('emp_again')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('references');
    }
}
