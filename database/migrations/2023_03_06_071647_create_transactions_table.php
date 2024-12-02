<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('complaint_id');
            $table->foreign('complaint_id')->references('id')->on('complaints')->onDelete('cascade')->onUpdate('cascade');
            $table->string('invoiceId');
            $table->string('invoiceURL');
            $table->string('paymentId')->nullable();
            $table->double('invoice_value');
            $table->string('customer_name')->nullable();
            $table->string('customer_phone')->nullable();
            $table->string('card_number')->nullable();
            $table->string('transaction_status')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transactions');
    }
};
