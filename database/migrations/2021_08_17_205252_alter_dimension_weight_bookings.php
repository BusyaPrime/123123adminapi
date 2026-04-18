<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterDimensionWeightBookings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('truck_bookings', function (Blueprint $table) {
            $table->string('dimension_x')->nullable()->default(null)->change();
            $table->string('dimension_y')->nullable()->default(null)->change();
            $table->string('dimension_z')->nullable()->default(null)->change();
            $table->string('weight')->nullable()->default(null)->change();
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
            $table->text('dimension_x')->nullable()->default(null)->change();
            $table->text('dimension_y')->nullable()->default(null)->change();
            $table->text('dimension_z')->nullable()->default(null)->change();
            $table->text('weight')->nullable()->default(null)->change();
        });
    }
}
