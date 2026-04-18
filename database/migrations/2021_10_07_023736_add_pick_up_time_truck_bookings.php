<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPickUpTimeTruckBookings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('truck_bookings', function (Blueprint $table) {
            $table->string('pickup_time')->nullable()->default(null);
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
            $table->dropColumn('pickup_time');
        });
    }
}
