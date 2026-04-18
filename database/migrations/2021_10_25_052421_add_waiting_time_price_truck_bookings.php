<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddWaitingTimePriceTruckBookings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('truck_bookings', function (Blueprint $table) {

            $table->bigInteger('pickup_limit')->nullable()->default(null);
            $table->bigInteger('pickup_per_minute')->nullable()->default(null);
            $table->bigInteger('pickup_waiting_time')->nullable()->default(null);
            $table->bigInteger('pickup_overtime')->nullable()->default(null);
            $table->bigInteger('pickup_price')->nullable()->default(null);

            $table->bigInteger('unloading_limit')->nullable()->default(null);
            $table->bigInteger('unloading_per_minute')->nullable()->default(null);
            $table->bigInteger('unloading_waiting_time')->nullable()->default(null);
            $table->bigInteger('unloading_overtime')->nullable()->default(null);
            $table->bigInteger('unloading_price')->nullable()->default(null);

            $table->bigInteger('delivery_price')->nullable()->default(null);


        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('truck_bookings', function (Blueprint $table) {
            $table->dropColumn('pickup_limit');
            $table->dropColumn('pickup_per_minute');
            $table->dropColumn('pickup_waiting_time');
            $table->dropColumn('pickup_overtime');
            $table->dropColumn('pickup_price');
            $table->dropColumn('unloading_limit');
            $table->dropColumn('unloading_per_minute');
            $table->dropColumn('unloading_waiting_time');
            $table->dropColumn('unloading_overtime');
            $table->dropColumn('unloading_price');
            $table->dropColumn('delivery_price');
        });
    }
}
