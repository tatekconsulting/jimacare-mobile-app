<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateTimesheetPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        if (!Schema::hasTable('timesheet_payments')) {
            // First, create the table without foreign keys
            Schema::create('timesheet_payments', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('client_id');
                $table->unsignedBigInteger('carer_id'); // carer, childminder, or housekeeper
                $table->unsignedBigInteger('contract_id')->nullable();
                $table->string('period_type'); // 'weekly' or 'monthly'
                $table->date('period_start');
                $table->date('period_end');
                $table->decimal('total_hours', 8, 2)->default(0);
                $table->decimal('hourly_rate', 8, 2);
                $table->decimal('subtotal', 10, 2);
                $table->decimal('platform_fee', 10, 2)->default(0); // Platform commission
                $table->decimal('total_amount', 10, 2);
                $table->string('stripe_payment_link_id')->nullable(); // Stripe Payment Link ID
                $table->string('stripe_payment_link_url')->nullable(); // Stripe Payment Link URL
                $table->enum('status', ['pending', 'link_sent', 'paid', 'failed', 'cancelled'])->default('pending');
                $table->timestamp('link_sent_at')->nullable();
                $table->timestamp('paid_at')->nullable();
                $table->string('stripe_payment_intent_id')->nullable();
                $table->json('timesheet_ids')->nullable(); // Array of timesheet IDs included in this payment
                $table->text('notes')->nullable();
                $table->timestamps();
                
                $table->index(['client_id', 'carer_id', 'status']);
                $table->index(['period_start', 'period_end']);
            });

            // Add foreign keys separately (only if tables exist and are InnoDB)
            try {
                // Check if users table exists and get its engine
                $usersTableInfo = DB::select("SHOW TABLE STATUS WHERE Name = 'users'");
                $contractsTableInfo = DB::select("SHOW TABLE STATUS WHERE Name = 'contracts'");
                
                if (!empty($usersTableInfo) && !empty($contractsTableInfo)) {
                    $usersEngine = $usersTableInfo[0]->Engine ?? null;
                    $contractsEngine = $contractsTableInfo[0]->Engine ?? null;
                    
                    // Only add foreign keys if tables are InnoDB
                    if ($usersEngine === 'InnoDB') {
                        Schema::table('timesheet_payments', function (Blueprint $table) {
                            $table->foreign('client_id')->references('id')->on('users')->onDelete('cascade');
                            $table->foreign('carer_id')->references('id')->on('users')->onDelete('cascade');
                        });
                    }
                    
                    if ($contractsEngine === 'InnoDB' && Schema::hasTable('contracts')) {
                        Schema::table('timesheet_payments', function (Blueprint $table) {
                            $table->foreign('contract_id')->references('id')->on('contracts')->onDelete('set null');
                        });
                    }
                }
            } catch (\Exception $e) {
                // If foreign key creation fails, continue without them
                // The application will still work, just without referential integrity
                \Log::warning('Could not create foreign keys for timesheet_payments: ' . $e->getMessage());
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('timesheet_payments');
    }
}
