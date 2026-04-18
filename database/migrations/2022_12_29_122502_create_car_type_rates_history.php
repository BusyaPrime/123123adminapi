<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCarTypeRatesHistory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('car_type_rates_histories')) {
            Schema::create('car_type_rates_histories', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->bigInteger('car_type_id')->unsigned();
                $table->bigInteger('car_type_rate_id')->unsigned();
                $table->bigInteger('region_from_id')->unsigned()->nullable()->default(null);
                $table->bigInteger('region_to_id')->unsigned()->nullable()->default(null);
                $table->bigInteger('season_id')->unsigned()->nullable()->default(null);
                $table->text('changed_fields')->nullable()->default(null);
                $table->foreign('car_type_rate_id')->references('id')->on('car_type_rates');

                $table->timestamps();
            });   
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('car_type_rates_history');
    }
}
