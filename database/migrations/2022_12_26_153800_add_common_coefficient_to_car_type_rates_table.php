<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCommonCoefficientToCarTypeRatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('car_type_rates', 'common_coefficient')) {
            Schema::table('car_type_rates', function (Blueprint $table) {
                $table->string('common_coefficient')->default(1);
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
