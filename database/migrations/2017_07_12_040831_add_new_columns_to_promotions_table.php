<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNewColumnsToPromotionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('promotions', function ($table) {
            $table->dateTime('expire_date')->nullable();
            $table->dateTime('offer_end_date')->nullable();
        });
    }


}
