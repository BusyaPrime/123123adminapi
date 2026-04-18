<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCarsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cars', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('user_id')->unsigned();
            $table->bigInteger('car_type_id')->unsigned();
            $table->string('model')->nullable()->default(null);
            $table->string('color')->nullable()->default(null);
            $table->string('number')->nullable()->default(null);
            $table->bigInteger('max_weight')->unsigned()->nullable()->default(null);
            $table->bigInteger('dimension_x')->unsigned()->nullable()->default(null);
            $table->bigInteger('dimension_y')->unsigned()->nullable()->default(null);
            $table->bigInteger('dimension_z')->unsigned()->nullable()->default(null);
            $table->boolean('can_pack')->nullable()->default(null);
            $table->boolean('can_provide_loader')->nullable()->default(null);
            $table->timestamps();

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            $table->foreign('car_type_id')
                ->references('id')
                ->on('car_types')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cars');
    }
}
