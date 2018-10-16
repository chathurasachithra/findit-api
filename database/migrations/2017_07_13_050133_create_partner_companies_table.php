<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePartnerCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('partner_companies', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('promotion_id');
            $table->integer('partner_company_id');
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
        Schema::drop('partner_companies');
    }
}
