<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddParamsCarTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('car_types', function (Blueprint $table) {
            $table->bigInteger('max_weight')->unsigned()->nullable()->default(null);
            $table->float('dimension_x', 5, 2)->unsigned()->nullable()->default(null);
            $table->float('dimension_y', 5, 2)->unsigned()->nullable()->default(null);
            $table->float('dimension_z', 5, 2)->unsigned()->nullable()->default(null);
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
            $table->dropColumn('max_weight');
            $table->dropColumn('dimension_x');
            $table->dropColumn('dimension_y');
            $table->dropColumn('dimension_z');
        });
    }
}
