<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLimitedDirectionsLimitedWeightToCarTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('car_types', 'limited_directions') && !Schema::hasColumn('car_types', 'limited_weight')) {
            Schema::table('car_types', function (Blueprint $table) {
                $table->text('limited_directions')->nullable()->default(null);
                $table->bigInteger('limited_weight')->nullable()->default(null);
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
        Schema::table('car_types', function (Blueprint $table) {
            //
        });
    }
}
