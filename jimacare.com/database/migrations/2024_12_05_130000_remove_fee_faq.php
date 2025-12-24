<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class RemoveFeeFaq extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Remove FAQ about fees (check various title formats)
        DB::table('faqs')
            ->where('title', 'LIKE', '%How much does Jimacare charge%')
            ->orWhere('title', 'LIKE', '%How much does Jimacare charges%')
            ->orWhere(function($query) {
                $query->where('title', 'LIKE', '%fee%')
                      ->where('title', 'LIKE', '%charge%');
            })
            ->orWhere('desc', 'LIKE', '%JimaCare no longer collects fees or processes payments%')
            ->orWhere('desc', 'LIKE', '%no longer collects fees%')
            ->delete();
        
        // Remove FAQ about self-employment
        DB::table('faqs')
            ->where('title', 'LIKE', '%Must I be self employ%')
            ->orWhere('title', 'LIKE', '%self employ%')
            ->orWhere('desc', 'LIKE', '%Please contact our support team for current employment arrangements and requirements%')
            ->orWhere('desc', 'LIKE', '%contact our support team for current employment%')
            ->delete();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // This migration cannot be reversed as we don't know the exact original content
        // If needed, restore from backup
    }
}

