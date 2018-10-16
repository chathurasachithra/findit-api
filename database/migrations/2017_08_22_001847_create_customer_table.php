<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCustomerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->increments('customer_id');
            $table->string('fist_name');
            $table->string('second_name');
            $table->integer('age');
            $table->integer('province');
            $table->string('mobile_no');
            $table->string('land_no')->nullable();
            $table->string('email');
            $table->string('gender');
            $table->dateTime('created_at');
            $table->dateTime('modified_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('customers');
    }
}
