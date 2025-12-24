<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJobInvitationsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('job_invitations')) {
            Schema::create('job_invitations', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('contract_id'); // Job/Contract
                $table->unsignedBigInteger('client_id'); // Who invited
                $table->unsignedBigInteger('carer_id'); // Who was invited (carer, childminder, housekeeper)
                $table->enum('status', ['pending', 'accepted', 'rejected'])->default('pending');
                $table->text('message')->nullable(); // Optional message from client
                $table->timestamp('invited_at')->useCurrent();
                $table->timestamp('responded_at')->nullable();
                $table->text('rejection_reason')->nullable();
                $table->timestamps();

                // Foreign keys
                $table->foreign('contract_id')->references('id')->on('contracts')->onDelete('cascade');
                $table->foreign('client_id')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('carer_id')->references('id')->on('users')->onDelete('cascade');

                // Prevent duplicate invitations
                $table->unique(['contract_id', 'carer_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_invitations');
    }
}

