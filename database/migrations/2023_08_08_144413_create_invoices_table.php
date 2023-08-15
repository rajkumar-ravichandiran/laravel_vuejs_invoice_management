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
        Schema::create('invoices', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('number',100);
            $table->string('gst_no',15);
            $table->date('date')->nullable();
            $table->string('status',200);
            $table->date('due_date')->nullable();
            $table->string('reference_number',100);
            $table->unsignedBigInteger('customer_id');
            $table->index('customer_id');
            $table->foreign('customer_id')->references('id')->on('customers');
            $table->integer('currency_id');
            $table->double('discount', 10, 2)->nullable();
            $table->double('discounted_amount', 10, 2)->nullable();
            $table->tinyInteger('is_discount_before_tax')->default(0);
            $table->integer('discount_type');
            $table->text('line_items')->nullable()->default(NULL);
            $table->string('shipping_charge',15);
            $table->double('adjustment', 10, 2)->nullable();
            $table->string('adjustment_description',100);
            $table->float('net', 10, 2)->nullable();
            $table->float('sub_total', 10, 2)->nullable();
            $table->double('tax_total', 10, 2)->nullable();
            $table->double('total', 10, 2)->nullable();
            $table->float('payment_made', 10, 2)->nullable();
            $table->string('balance',15);
            $table->unsignedBigInteger('billing_address');
            $table->index('billing_address');
            $table->foreign('billing_address')->references('id')->on('address');
            $table->unsignedBigInteger('shipping_address');
            $table->index('shipping_address');
            $table->foreign('shipping_address')->references('id')->on('address');
            $table->text('notes')->nullable()->default(NULL);
            $table->text('terms')->nullable()->default(NULL);
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
        Schema::dropIfExists('invoices');
    }
};
