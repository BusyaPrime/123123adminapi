<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSizeTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('size_translations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('size_id')->unsigned();
            $table->string('title')->nullable()->default(null);

            $table->string('locale')->index();

            $table->unique(['size_id', 'locale']);

            $table->foreign('size_id')
                ->references('id')
                ->on('sizes')
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
        Schema::dropIfExists('size_translations');
    }
}
