<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHourliesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hourlies', function (Blueprint $table) {
	        $table->foreignId('day_id');//->constrained();
            $table->foreignId('contract_id');//->constrained();
            $table->string('morning')->nullable();
	        $table->string('afternoon')->nullable();
	        $table->string('evening')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('hourlies');
    }
}
