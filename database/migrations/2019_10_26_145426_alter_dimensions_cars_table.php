<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterDimensionsCarsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cars', function (Blueprint $table) {
            $table->float('dimension_x', 5, 2)->unsigned()->nullable()->default(null)->change();
            $table->float('dimension_y', 5, 2)->unsigned()->nullable()->default(null)->change();
            $table->float('dimension_z', 5, 2)->unsigned()->nullable()->default(null)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cars', function (Blueprint $table) {
            $table->bigInteger('dimension_x')->unsigned()->nullable()->default(null)->change();
            $table->bigInteger('dimension_y')->unsigned()->nullable()->default(null)->change();
            $table->bigInteger('dimension_z')->unsigned()->nullable()->default(null)->change();
        });
    }
}
