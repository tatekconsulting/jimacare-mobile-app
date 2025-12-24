<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNewFeatureTables extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        // Job Applications table
        if (!Schema::hasTable('job_applications')) {
            Schema::create('job_applications', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('contract_id');
                $table->unsignedBigInteger('carer_id');
                $table->text('cover_letter')->nullable();
                $table->decimal('proposed_rate', 8, 2)->nullable();
                $table->enum('status', ['pending', 'accepted', 'rejected', 'withdrawn'])->default('pending');
                $table->timestamp('responded_at')->nullable();
                $table->text('rejection_reason')->nullable();
                $table->timestamps();
                
                $table->foreign('contract_id')->references('id')->on('contracts')->onDelete('cascade');
                $table->foreign('carer_id')->references('id')->on('users')->onDelete('cascade');
                
                // Prevent duplicate applications
                $table->unique(['contract_id', 'carer_id']);
            });
        }

        // Timesheets table
        if (!Schema::hasTable('timesheets')) {
            Schema::create('timesheets', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('contract_id');
                $table->unsignedBigInteger('carer_id');
                $table->unsignedBigInteger('client_id');
                $table->date('date');
                $table->timestamp('clock_in')->nullable();
                $table->timestamp('clock_out')->nullable();
                $table->decimal('hours_worked', 8, 2)->nullable();
                $table->decimal('hourly_rate', 8, 2)->default(0);
                $table->decimal('total_amount', 10, 2)->nullable();
                $table->text('notes')->nullable();
                $table->enum('status', ['pending', 'approved', 'disputed', 'paid'])->default('pending');
                $table->timestamp('approved_at')->nullable();
                $table->text('dispute_reason')->nullable();
                $table->decimal('location_lat', 10, 8)->nullable();
                $table->decimal('location_lng', 11, 8)->nullable();
                $table->timestamps();
                
                $table->foreign('contract_id')->references('id')->on('contracts')->onDelete('cascade');
                $table->foreign('carer_id')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('client_id')->references('id')->on('users')->onDelete('cascade');
            });
        }

        // Carer Locations table (for real-time tracking)
        if (!Schema::hasTable('carer_locations')) {
            Schema::create('carer_locations', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('carer_id');
                $table->decimal('latitude', 10, 8);
                $table->decimal('longitude', 11, 8);
                $table->boolean('is_active')->default(true);
                $table->timestamp('last_updated')->nullable();
                $table->timestamps();
                
                $table->foreign('carer_id')->references('id')->on('users')->onDelete('cascade');
                $table->unique('carer_id');
            });
        }

        // User Notifications table
        if (!Schema::hasTable('user_notifications')) {
            Schema::create('user_notifications', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->string('type')->nullable(); // e.g., 'job_application', 'timesheet_approved', etc.
                $table->string('title');
                $table->text('message');
                $table->string('action_url')->nullable();
                $table->json('data')->nullable();
                $table->boolean('is_read')->default(false);
                $table->timestamp('read_at')->nullable();
                $table->timestamps();
                
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->index(['user_id', 'is_read']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('user_notifications');
        Schema::dropIfExists('carer_locations');
        Schema::dropIfExists('timesheets');
        Schema::dropIfExists('job_applications');
    }
}

