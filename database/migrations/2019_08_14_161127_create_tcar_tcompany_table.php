<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTcarTcompanyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tcar_tcompany', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('tcar_id')->unsigned();
            $table->bigInteger('tcompany_id')->unsigned();
            $table->foreign('tcar_id')
                ->references('id')
                ->on('tcars')
                ->onDelete('cascade');
            $table->foreign('tcompany_id')
                ->references('id')
                ->on('tcompanies')
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
        Schema::dropIfExists('tcar_tcompany');
    }
}
