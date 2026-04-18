<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClientReviewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('client_reviews')) {
            Schema::create('client_reviews', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('company');
                $table->string('director')->nullable();
                $table->string('director_role')->nullable();
                $table->string('logo')->nullable();
                $table->text('review')->nullable();
                $table->boolean('published', 1)->default(false);
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('client_reviews');
    }
}
