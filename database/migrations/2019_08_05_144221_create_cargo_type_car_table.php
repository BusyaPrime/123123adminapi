<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCargoTypeCarTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('car_cargo_type', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('cargo_type_id')->unsigned();
            $table->bigInteger('car_id')->unsigned();
            $table->foreign('car_id')
                ->references('id')
                ->on('cars')
                ->onDelete('cascade');
            $table->foreign('cargo_type_id')
                ->references('id')
                ->on('car_types')
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
        Schema::dropIfExists('car_cargo_type');
    }
}
