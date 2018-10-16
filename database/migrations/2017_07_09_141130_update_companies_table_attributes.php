<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateCompaniesTableAttributes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('companies', function ($table) {
            $table->string('company_tel1', 15)->nullable()->change();
            $table->string('company_address')->nullable()->change();
            $table->string('company_email', 100)->nullable()->change();
            $table->string('company_email', 100)->nullable()->change();
            $table->string('company_logo')->nullable()->change();
        });
    }
}
