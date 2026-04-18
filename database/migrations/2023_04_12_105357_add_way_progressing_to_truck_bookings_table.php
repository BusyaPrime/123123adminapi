<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddWayProgressingToTruckBookingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('truck_bookings', 'way_in_progress')) {
            Schema::table('truck_bookings', function (Blueprint $table) {
                $table->bigInteger('way_in_progress')->default(1);
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
