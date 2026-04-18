<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSizesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sizes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('icon')->nullable()->default(null);
            $table->float('dimension_x', 5, 2)->unsigned()->nullable()->default(null);
            $table->float('dimension_y', 5, 2)->unsigned()->nullable()->default(null);
            $table->float('dimension_z', 5, 2)->unsigned()->nullable()->default(null);
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
        Schema::dropIfExists('sizes');
    }
}
