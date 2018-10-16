<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePromotionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('promotions', function (Blueprint $table) {
            $table->increments('promotion_id');
            $table->string('promotion_name');
            $table->string('promotion_description');
            $table->string('promotion_image');
            $table->integer('promotion_views');
            $table->string('promotion_is_featured');
            $table->integer('category_id');
            $table->integer('company_id');
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
        Schema::drop('promotions');
    }
}
