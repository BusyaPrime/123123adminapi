<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddParamsCarTypeRates extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('car_type_rates', function (Blueprint $table) {
            $table->string('ratio')->nullable()->default(null);
            $table->string('not_full_min_price')->nullable()->default(null);
            $table->string('not_full_min_value')->nullable()->default(null);
            $table->string('not_full_ratio')->nullable()->default(null);
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
            $table->dropColumn('ratio');
            $table->dropColumn('not_full_min_price');
            $table->dropColumn('not_full_min_value');
            $table->dropColumn('not_full_ratio');
        });
    }
}
