<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInstantBookingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('instant_bookings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('invoice_id')->nullable();
            $table->unsignedBigInteger('client_id');
            $table->unsignedBigInteger('carer_id');
            $table->date('date');
            $table->time('start_time');
            $table->time('end_time');
            $table->decimal('hourly_rate', 8, 2);
            $table->decimal('estimated_price', 10, 2);
            $table->enum('status', ['pending', 'accepted', 'declined', 'expired', 'completed', 'cancelled'])->default('pending');
            $table->text('decline_reason')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('location_sharing_started')->nullable();
            $table->timestamps();

            $table->index(['carer_id', 'date', 'status']);
            $table->index(['client_id', 'status']);
        });

        // Video calls table
        Schema::create('video_calls', function (Blueprint $table) {
            $table->id();
            $table->string('room_id')->unique();
            $table->unsignedBigInteger('initiated_by');
            $table->unsignedBigInteger('recipient_id');
            $table->unsignedBigInteger('booking_id')->nullable();
            $table->enum('status', ['pending', 'active', 'ended', 'missed'])->default('pending');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('ended_at')->nullable();
            $table->integer('duration_seconds')->nullable();
            $table->timestamps();

            $table->index(['room_id', 'status']);
        });

        // Location tracking table
        Schema::create('location_tracks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('booking_id');
            $table->unsignedBigInteger('user_id');
            $table->decimal('lat', 10, 8);
            $table->decimal('long', 11, 8);
            $table->decimal('accuracy', 8, 2)->nullable();
            $table->integer('eta_minutes')->nullable();
            $table->timestamps();

            $table->index(['booking_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('location_tracks');
        Schema::dropIfExists('video_calls');
        Schema::dropIfExists('instant_bookings');
    }
}