<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCancelReasonTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cancel_reason_translations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('cancel_reason_id')->unsigned();
            $table->string('locale')->index();

            $table->string('reason');

            $table->unique(['cancel_reason_id', 'locale']);
            $table->foreign('cancel_reason_id')->references('id')->on('cancel_reasons')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cancel_reason_translations');
    }
}
