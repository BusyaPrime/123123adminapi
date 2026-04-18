<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOctoTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('octo_transactions', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->bigInteger('user_id')->unsigned()->nullable()->default(null);

            $table->boolean('error')->nullable()->default(null);
            $table->string('error_message')->nullable()->default(null);
            $table->string('status')->nullable()->default(null);
            $table->string('octo_payment_id')->nullable()->default(null);
            $table->string('octo_pay_url')->nullable()->default(null);
            $table->string('total_sum')->nullable()->default(null);

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
        Schema::dropIfExists('octo_transactions');
    }
}
