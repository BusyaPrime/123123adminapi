<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIsMultiRegionToCarTypeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('car_types', 'is_multi_region')) {
            Schema::table('car_types', function (Blueprint $table) {
                $table->tinyInteger('is_multi_region')->default(1);
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
