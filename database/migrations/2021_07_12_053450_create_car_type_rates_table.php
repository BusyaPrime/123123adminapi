<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCarTypeRatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('car_type_rates', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('car_type_id')->unsigned();
            $table->bigInteger('region_from_id')->unsigned()->nullable()->default(null);
            $table->bigInteger('region_to_id')->unsigned()->nullable()->default(null);
            $table->bigInteger('season_id')->unsigned()->nullable()->default(null);
            $table->longText('prices')->nullable()->default(null);
            $table->longText('time_ratio')->nullable()->default(null);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('car_type_rates');
    }
}
