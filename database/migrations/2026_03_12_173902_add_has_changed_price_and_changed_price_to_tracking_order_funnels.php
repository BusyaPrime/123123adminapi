<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddHasChangedPriceAndChangedPriceToTrackingOrderFunnels extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tracking_order_funnels', function (Blueprint $table) {
            $table->tinyInteger('has_changed_price')->nullable()->default(0);
            $table->unsignedInteger('changed_price')->nullable();
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
