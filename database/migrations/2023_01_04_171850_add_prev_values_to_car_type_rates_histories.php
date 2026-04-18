<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPrevValuesToCarTypeRatesHistories extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('car_type_rates_histories', 'previous_values')) {
            Schema::table('car_type_rates_histories', function (Blueprint $table) {
                $table->text('previous_values')->nullable()->default(null);
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
        Schema::table('car_type_rates_histories', function (Blueprint $table) {
            //
        });
    }
}
