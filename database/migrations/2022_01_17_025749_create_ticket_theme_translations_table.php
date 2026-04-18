<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTicketThemeTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ticket_theme_translations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('ticket_theme_id')->unsigned();
            $table->string('title')->nullable()->default(null);


            $table->string('locale')->index();

            $table->unique(['ticket_theme_id', 'locale']);

            $table->foreign('ticket_theme_id')
                ->references('id')
                ->on('ticket_themes')
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
        Schema::dropIfExists('ticket_theme_translations');
    }
}
