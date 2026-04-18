<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTcarBookingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tcar_bookings', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->longText('routes')->nullable()->default(null);
            $table->bigInteger('user_id')->unsigned()->nullable()->default(null);
            $table->bigInteger('driver_id')->unsigned()->nullable()->default(null);
            $table->bigInteger('tcar_type_id')->unsigned()->nullable()->default(null);
            $table->bigInteger('price')->unsigned()->nullable()->default(0);
            $table->bigInteger('commission')->unsigned()->nullable()->default(0);

            $table->string('date')->nullable()->default(null);
            $table->string('time')->nullable()->default(null);
            $table->integer('peoples')->unsigned()->nullable()->default(null);
            $table->boolean('ac')->nullable()->default(null);
            $table->boolean('kids_seat')->nullable()->default(null);

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
        Schema::dropIfExists('tcar_bookings');
    }
}
