<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJobApplicationsTable extends Migration
{
    public function up()
    {
        Schema::create('job_applications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('contract_id');
            $table->unsignedBigInteger('carer_id');
            $table->enum('status', ['pending', 'accepted', 'rejected', 'withdrawn'])->default('pending');
            $table->text('message')->nullable();
            $table->decimal('proposed_rate', 8, 2)->nullable();
            $table->integer('match_score')->nullable();
            $table->timestamp('client_viewed_at')->nullable();
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->timestamps();

            $table->unique(['contract_id', 'carer_id']);
            $table->index(['contract_id', 'status']);
            $table->index(['carer_id', 'status']);
        });

        Schema::create('timesheets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('job_application_id');
            $table->unsignedBigInteger('contract_id');
            $table->unsignedBigInteger('carer_id');
            $table->unsignedBigInteger('client_id');
            $table->date('work_date');
            $table->timestamp('clock_in')->nullable();
            $table->timestamp('clock_out')->nullable();
            $table->decimal('hours_worked', 5, 2)->nullable();
            $table->decimal('hourly_rate', 8, 2)->nullable();
            $table->decimal('total_pay', 10, 2)->nullable();
            $table->text('notes')->nullable();
            $table->enum('status', ['pending', 'approved', 'disputed'])->default('pending');
            $table->timestamp('client_approved_at')->nullable();
            $table->timestamps();

            $table->index(['carer_id', 'work_date']);
            $table->index(['client_id', 'work_date']);
            $table->index(['contract_id', 'status']);
        });

        Schema::create('carer_locations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('job_application_id');
            $table->unsignedBigInteger('carer_id');
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            $table->decimal('accuracy', 8, 2)->nullable();
            $table->boolean('is_en_route')->default(false);
            $table->boolean('has_arrived')->default(false);
            $table->timestamps();

            $table->index(['job_application_id', 'created_at']);
        });

        Schema::create('user_notifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('type');
            $table->string('title');
            $table->text('message');
            $table->json('data')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'read_at']);
            $table->index(['user_id', 'type']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_notifications');
        Schema::dropIfExists('carer_locations');
        Schema::dropIfExists('timesheets');
        Schema::dropIfExists('job_applications');
    }
}
