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
        Schema::create('customers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name',200);
            $table->string('email',200);
            $table->string('phone',200);
            $table->tinyInteger('type')->default(0);
            $table->string('company_name',200);
            $table->unsignedBigInteger('payment_terms');
            $table->integer('currency_id');
            $table->string('website',200);
            $table->text('custom_fields')->nullable()->default(NULL);
            $table->unsignedBigInteger('billing_address');
            $table->index('billing_address');
            $table->foreign('billing_address')->references('id')->on('address');
            $table->unsignedBigInteger('shipping_address');
            $table->index('shipping_address');
            $table->foreign('shipping_address')->references('id')->on('address');
            $table->text('notes')->nullable()->default(NULL);
            $table->text('contact_persons')->nullable()->default(NULL);
            $table->string('gst_no',100)->nullable()->default(NULL);
            $table->string('facebook',100)->nullable()->default(NULL);
            $table->string('twitter',100)->nullable()->default(NULL);
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
        Schema::dropIfExists('customers');
    }
};
