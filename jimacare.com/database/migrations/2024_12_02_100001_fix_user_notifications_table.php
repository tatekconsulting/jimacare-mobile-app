<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FixUserNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        // If table exists but missing columns, add them
        if (Schema::hasTable('user_notifications')) {
            Schema::table('user_notifications', function (Blueprint $table) {
                // Check and add missing columns
                if (!Schema::hasColumn('user_notifications', 'is_read')) {
                    $table->boolean('is_read')->default(false)->after('data');
                }
                if (!Schema::hasColumn('user_notifications', 'read_at')) {
                    $table->timestamp('read_at')->nullable()->after('is_read');
                }
                if (!Schema::hasColumn('user_notifications', 'type')) {
                    $table->string('type')->nullable()->after('user_id');
                }
                if (!Schema::hasColumn('user_notifications', 'action_url')) {
                    $table->string('action_url')->nullable()->after('message');
                }
                if (!Schema::hasColumn('user_notifications', 'data')) {
                    $table->json('data')->nullable()->after('action_url');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        // Don't remove columns in case they're needed
    }
}