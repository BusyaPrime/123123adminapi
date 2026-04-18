<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCarCompanyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('car_company', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('car_id')->unsigned();
            $table->bigInteger('company_id')->unsigned();
            $table->foreign('car_id')
                ->references('id')
                ->on('cars')
                ->onDelete('cascade');
            $table->foreign('company_id')
                ->references('id')
                ->on('companies')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('car_company');
    }
}
