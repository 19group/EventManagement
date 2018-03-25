<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAcccommodationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('acccommodations', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps('time');
            $table->string('full_name');
            $table->string('hotel_status');
            $table->string('title');
            $table->string('email');
            $table->string('amount');
            $table->string('days')->nullable();
            $table->string('dates');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('acccommodations');
    }
}
