<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EditCarTypeRatesOverLimitCoeffColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('car_type_rates', function (Blueprint $table) {
            $table->integer('distance_between')->nullable()->default(null)->change();
            $table->string('over_limit_coeff')->nullable()->default(null)->change();
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
