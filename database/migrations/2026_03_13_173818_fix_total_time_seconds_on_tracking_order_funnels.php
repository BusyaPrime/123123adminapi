<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class FixTotalTimeSecondsOnTrackingOrderFunnels extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tracking_order_funnels', function (Blueprint $table) {
            $table->unsignedInteger('total_time_seconds')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tracking_order_funnels', function (Blueprint $table) {
            $table->timestamp('total_time_seconds')->nullable()->change();
        });
    }
}
