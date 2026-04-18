<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTicketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->bigInteger('user_id')->unsigned()->nullable()->default(null);
            $table->string('user_type')->nullable()->default(null);
            $table->string('user_name')->nullable()->default(null);

            $table->string('subject')->nullable()->default(null);
            $table->longText('text')->nullable()->default(null);
            $table->string('file')->nullable()->default(null);

            $table->string('status')->nullable()->default(null);
            $table->longText('admin_comment')->nullable()->default(null);

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
        Schema::dropIfExists('tickets');
    }
}
