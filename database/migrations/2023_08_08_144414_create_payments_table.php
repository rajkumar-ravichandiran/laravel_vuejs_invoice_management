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
        Schema::create('payments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('number',100);
            $table->string('payment_mode',100);
            $table->double('amount', 10, 2)->nullable();
            $table->double('amount_refunded', 10, 2)->nullable();
            $table->double('bank_charges', 10, 2)->nullable();
            $table->date('date')->nullable();
            $table->string('status',100);
            $table->string('reference_number',100);
            $table->string('description',2000);
            $table->unsignedBigInteger('customer_id');
            $table->index('customer_id');
            $table->foreign('customer_id')->references('id')->on('customers');
            $table->unsignedBigInteger('invoice_id');
            $table->index('invoice_id');
            $table->foreign('invoice_id')->references('id')->on('invoices');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payments');
    }
};
