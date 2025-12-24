<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Set default radius of 10 miles for existing contracts that don't have a radius
        DB::table('contracts')
            ->whereNull('radius')
            ->update(['radius' => 10]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Optionally, you can set radius back to null if needed
        // This is optional - you may not want to reverse this
        // DB::table('contracts')
        //     ->where('radius', 10)
        //     ->update(['radius' => null]);
    }
};

