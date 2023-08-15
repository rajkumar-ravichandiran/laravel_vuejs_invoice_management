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
        Schema::create('items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name',100);
            $table->tinyInteger('active')->default(1);
            $table->double('rate', 10, 2)->nullable();
            $table->string('description',2000);
            $table->string('tax_id',200);
            $table->string('sku',200);
            $table->tinyInteger('type')->default(0);
            $table->tinyInteger('is_taxable')->default(0);
            $table->string('hsn_or_sac',20);
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
        Schema::dropIfExists('items');
    }
};
