<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAdditionalCarTypes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('car_types', function (Blueprint $table) {
            $table->string('icon')->nullable()->default(null);
            $table->integer('min_distance')->nullable()->default(0);
            $table->integer('min_price')->nullable()->default(0);
            $table->bigInteger('price_per_km')->nullable()->default(0);
            $table->bigInteger('price_per_min')->nullable()->default(0);
            $table->double('commission', 4, 2)->unsigned()->default(0)->nullable();
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
            $table->dropColumn('icon');
            $table->dropColumn('min_distance');
            $table->dropColumn('min_price');
            $table->dropColumn('price_per_km');
            $table->dropColumn('commission');
        });
    }
}
