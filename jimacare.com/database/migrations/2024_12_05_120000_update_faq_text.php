<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class UpdateFaqText extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Update the FAQ text for "How can I get carer through this platform?"
        DB::table('faqs')
            ->where('title', 'How can I get carer through this platform?')
            ->update([
                'desc' => 'Register on our platform and start hiring experienced carers.'
            ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Revert to original text
        DB::table('faqs')
            ->where('title', 'How can I get carer through this platform?')
            ->update([
                'desc' => 'Register on our platform and start contacting our experienced carers.'
            ]);
    }
}

