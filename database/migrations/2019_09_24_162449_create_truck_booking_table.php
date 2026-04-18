<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTruckBookingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('truck_bookings', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->longText('routes')->nullable()->default(null);
            $table->bigInteger('user_id')->unsigned()->nullable()->default(null);
            $table->bigInteger('driver_id')->unsigned()->nullable()->default(null);
            $table->bigInteger('car_type_id')->unsigned()->nullable()->default(null);
            $table->bigInteger('price')->unsigned()->nullable()->default(0);
            $table->bigInteger('commission')->unsigned()->nullable()->default(0);
            $table->bigInteger('cargo_type_id')->unsigned()->nullable()->default(null);
            $table->bigInteger('load_type_id')->unsigned()->nullable()->default(null);
            $table->bigInteger('weight')->unsigned()->nullable()->default(null);
            $table->bigInteger('dimension_x')->unsigned()->nullable()->default(null);
            $table->bigInteger('dimension_y')->unsigned()->nullable()->default(null);
            $table->bigInteger('dimension_z')->unsigned()->nullable()->default(null);
            $table->boolean('need_pack')->nullable()->default(null);
            $table->boolean('need_provide_loader')->nullable()->default(null);
            $table->tinyInteger('loader_amount')->nullable()->default(null);
            $table->longText('comment')->nullable()->default(null);
            $table->string('distance')->nullable()->default(0);
            $table->longText('review')->nullable()->default(null);
            $table->integer('rating')->nullable()->default(null);
            $table->string('status')->nullable()->default(null);

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
        Schema::dropIfExists('truck_bookings');
    }
}
