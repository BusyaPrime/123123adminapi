<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTcarTypeTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tcar_type_translations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('tcar_type_id')->unsigned();
            $table->string('title')->nullable()->default(null);

            $table->string('locale')->index();

            $table->unique(['tcar_type_id', 'locale']);

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
        Schema::dropIfExists('tcar_type_translations');
    }
}
