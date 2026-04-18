<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBookingPriceOffersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('booking_price_offers', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('booking_id');
            $table->unsignedBigInteger('driver_id');
            $table->unsignedBigInteger('amount');

            
            $table->foreign('driver_id')->references('id')->on('users');
            $table->foreign('booking_id')->references('id')->on('truck_bookings');
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
        Schema::dropIfExists('booking_price_offers');
    }
}
