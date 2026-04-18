<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MakeOrderIdColumnNullableOnTrackingOrderFunnels extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tracking_order_funnels', function (Blueprint $table) {
            $table->unsignedBigInteger('order_id')->nullable()->change();
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
            //
        });
    }
}
