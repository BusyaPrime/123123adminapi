<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLoadTypeTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('load_type_translations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('load_type_id')->unsigned();
            $table->string('title')->nullable()->default(null);

            $table->string('locale')->index();

            $table->unique(['load_type_id', 'locale']);

            $table->foreign('load_type_id')
                ->references('id')
                ->on('load_types')
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
        Schema::dropIfExists('load_type_translations');
    }
}
