<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNightliesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('nightlies', function (Blueprint $table) {
	        $table->foreignId('day_id');//->constrained();
	        $table->foreignId('contract_id');//->constrained();
            $table->time('start')->nullable();
	        $table->time('end')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('nightlies');
    }
}
