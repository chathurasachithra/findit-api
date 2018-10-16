<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->increments('company_id');
            $table->string('company_name');
            $table->string('company_description')->nullable();
            $table->string('company_tel1');
            $table->string('company_tel2')->nullable();
            $table->string('company_address');
            $table->string('company_email');
            $table->string('company_website')->nullable();
            $table->string('company_fb')->nullable();
            $table->string('company_twitter')->nullable();
            $table->string('company_linkedin')->nullable();
            $table->string('company_youtube')->nullable();
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
        Schema::drop('companies');
    }
}
