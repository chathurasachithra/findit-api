<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class FixForMigrationErrors extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('companies', function ($table) {
            $table->string('company_banner')->nullable()->change();
            $table->string('company_latitude')->nullable()->change();
            $table->string('company_longitude')->nullable()->change();
        });
    }
}
