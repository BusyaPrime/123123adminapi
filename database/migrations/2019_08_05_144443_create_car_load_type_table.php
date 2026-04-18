<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCarLoadTypeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('car_load_type', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('car_id')->unsigned();
            $table->bigInteger('load_type_id')->unsigned();
            $table->foreign('car_id')
                ->references('id')
                ->on('cars')
                ->onDelete('cascade');
            $table->foreign('load_type_id')
                ->references('id')
                ->on('load_types')
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
        Schema::dropIfExists('car_load_type');
    }
}
