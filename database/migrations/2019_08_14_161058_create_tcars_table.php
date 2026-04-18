<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTcarsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tcars', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('user_id')->unsigned();
            $table->bigInteger('tcar_type_id')->unsigned();
            $table->string('model')->nullable()->default(null);
            $table->string('color')->nullable()->default(null);
            $table->string('number')->nullable()->default(null);
            $table->integer('peoples')->unsigned()->nullable()->default(null);
            $table->boolean('ac')->nullable()->default(null);
            $table->boolean('kids_seat')->nullable()->default(null);
            $table->boolean('active')->nullable()->default(1);
            $table->timestamps();

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            $table->foreign('tcar_type_id')
                ->references('id')
                ->on('tcar_types')
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
        Schema::dropIfExists('tcars');
    }
}
