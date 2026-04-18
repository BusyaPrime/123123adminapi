<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddGroupLoadPriceColumnToCarTypeRates extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('car_type_rates', function (Blueprint $table) {
            $table->bigInteger('group_load_price')->default(0)->after('min_price');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('car_type_rates', function (Blueprint $table) {
            //
        });
    }
}
