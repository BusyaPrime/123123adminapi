<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddWaitingTimePriceCarTypes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('car_types', function (Blueprint $table) {
            $table->bigInteger('pickup_limit')->nullable()->default(null);
            $table->bigInteger('pickup_per_minute')->nullable()->default(null);

            $table->bigInteger('unloading_limit')->nullable()->default(null);
            $table->bigInteger('unloading_per_minute')->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('car_types', function (Blueprint $table) {
            $table->dropColumn('pickup_limit');
            $table->dropColumn('pickup_per_minute');
            $table->dropColumn('unloading_limit');
            $table->dropColumn('unloading_per_minute');
        });
    }
}
