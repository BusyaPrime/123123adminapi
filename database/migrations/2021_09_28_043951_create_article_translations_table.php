<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateArticleTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('article_translations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('article_id')->unsigned();
            $table->text('title')->nullable()->default(null);
            $table->text('short')->nullable()->default(null);
            $table->longText('full')->nullable()->default(null);
            $table->text('link')->nullable()->default(null);

            $table->string('locale')->index();

            $table->unique(['article_id', 'locale']);

            $table->foreign('article_id')
                ->references('id')
                ->on('articles')
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
        Schema::dropIfExists('article_translations');
    }
}
