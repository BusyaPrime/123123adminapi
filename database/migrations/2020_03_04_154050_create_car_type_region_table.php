<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCarTypeRegionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('car_type_region', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('car_type_id')->unsigned();
            $table->bigInteger('region_id')->unsigned();
            $table->bigInteger('price_per_km')->nullable()->default(0);

            $table->foreign('car_type_id')
                ->references('id')->on('car_types')
                ->onDelete('cascade');

            $table->foreign('region_id')
                ->references('id')->on('regions')
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
        Schema::dropIfExists('car_type_region');
    }
}
