<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTcarTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tcar_types', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('icon')->nullable()->default(null);
            $table->integer('min_distance')->nullable()->default(0);
            $table->integer('min_price')->nullable()->default(0);
            $table->bigInteger('price_per_km')->nullable()->default(0);
            $table->bigInteger('price_per_min')->nullable()->default(0);
            $table->double('commission', 4, 2)->unsigned()->default(0)->nullable();
            $table->integer('priority')->nullable()->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tcar_types');
    }
}
