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
        Schema::create('address', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('attention',200)->nullable()->default(NULL);
            $table->string('address',500)->nullable()->default(NULL);
            $table->string('street2',200)->nullable()->default(NULL);
            $table->string('city',200)->nullable()->default(NULL);
            $table->string('state',20)->nullable()->default(NULL);
            $table->string('country',20)->nullable()->default(NULL);
            $table->string('zipcode',10)->nullable()->default(NULL);
            $table->string('fax',20)->nullable()->default(NULL);
            $table->string('phone',15)->nullable()->default(NULL);
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
        Schema::dropIfExists('address');
    }
};
