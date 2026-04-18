<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterForeignCarCargoType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('car_cargo_type', function (Blueprint $table) {
            $table->dropForeign('car_cargo_type_cargo_type_id_foreign');

            $table->foreign('cargo_type_id')
                ->references('id')
                ->on('cargo_types')
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
        Schema::table('car_cargo_type', function (Blueprint $table) {

            $table->dropForeign('car_cargo_type_cargo_type_id_foreign');

            $table->foreign('cargo_type_id')
                ->references('id')
                ->on('car_types')
                ->onDelete('cascade');
        });
    }
}
