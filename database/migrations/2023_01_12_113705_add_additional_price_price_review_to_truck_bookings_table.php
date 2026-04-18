<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAdditionalPricePriceReviewToTruckBookingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('truck_bookings', 'additional_price') && !Schema::hasColumn('truck_bookings', 'additional_price_review')) {
            Schema::table('truck_bookings', function (Blueprint $table) {
                $table->bigInteger('additional_price')->nullable()->default(0);
                $table->string('additional_price_review')->nullable()->default(null);
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
        Schema::table('truck_bookings', function (Blueprint $table) {
            //
        });
    }
}
