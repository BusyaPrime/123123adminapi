<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieLdsCarTypeRates extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('car_type_rates', function (Blueprint $table) {
            $table->string('divider')->nullable()->default(null);
            $table->string('distance')->nullable()->default(null);
            $table->longText('prices_distance')->nullable()->default(null);
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
            $table->dropColumn('divider');
            $table->dropColumn('distance');
            $table->dropColumn('prices_distance');
        });
    }
}
