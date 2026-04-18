<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCarTypeTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('car_type_translations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('car_type_id')->unsigned();
            $table->string('title')->nullable()->default(null);

            $table->string('locale')->index();

            $table->unique(['car_type_id', 'locale']);

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
        Schema::dropIfExists('car_type_translations');
    }
}
