<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddComplianceStatusToDocumentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->enum('compliance_status', ['valid', 'expiring', 'expired'])->nullable()->after('expiration');
        });
        
        // Update existing records to set compliance_status based on expiration
        \DB::statement("
            UPDATE documents 
            SET compliance_status = CASE
                WHEN expiration IS NULL THEN NULL
                WHEN expiration < CURDATE() THEN 'expired'
                WHEN expiration <= DATE_ADD(CURDATE(), INTERVAL 30 DAY) THEN 'expiring'
                ELSE 'valid'
            END
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropColumn(['compliance_status', 'expiry_date']);
        });
    }
}

