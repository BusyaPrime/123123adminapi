<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDistanceBetweenOverLimitCoeff extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('car_type_rates', 'distance_between') && !Schema::hasColumn('car_type_rates', 'over_limit_coeff')) {
            Schema::table('car_type_rates', function (Blueprint $table) {
                $table->integer('distance_between')->nullable()->default(null);
                $table->string('over_limit_coeff')->nullabel()->default(null);
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
        Schema::table('car_type_rates', function (Blueprint $table) {
            //
        });
    }
}
