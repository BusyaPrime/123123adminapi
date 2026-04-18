<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTcarBookingTcarTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tcar_tcar_booking', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('tcar_id')->unsigned();
            $table->bigInteger('tcar_booking_id')->unsigned();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tcar_tcar_booking');
    }
}
