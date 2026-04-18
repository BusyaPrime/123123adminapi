<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCargoTypeTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cargo_type_translations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('cargo_type_id')->unsigned();
            $table->string('title')->nullable()->default(null);


            $table->string('locale')->index();

            $table->unique(['cargo_type_id', 'locale']);

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
        Schema::dropIfExists('cargo_type_translations');
    }
}
