<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddClientPriceToBookingPriceOffersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('booking_price_offers', function (Blueprint $table) {
            $table->bigInteger('client_amount')->nullable()->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('booking_price_offers', function (Blueprint $table) {
            $table->dorpColumn('client_amount');
        });
    }
}
