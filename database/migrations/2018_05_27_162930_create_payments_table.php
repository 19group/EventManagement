<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->increments('id');
            $table->string('full_name')->nullable();
            $table->string('payer_email')->nullable();
            $table->string('receiver_email')->nullable();
            $table->string('payer_status')->nullable();
            $table->string('payment_status')->nullable();
            $table->string('amount')->nullable();
            $table->string('currency')->nullable();
            $table->string('payment_date')->nullable();
            $table->string('txn_id')->nullable();
            $table->text('custom')->nullable();
            $table->text('bought_tickets')->nullable();
            $table->text('order_details')->nullable();
            $table->boolean('paypal_verified')->default(false);
            $table->boolean('order_completed')->default(false);
            $table->boolean('transaction_approved')->default(false);
            $table->nullableTimestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('payments');
    }
}
