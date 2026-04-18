<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTcompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tcompanies', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('title');
            $table->string('contract_number')->nullable();
            $table->text('address')->nullable();
            $table->text('phones')->nullable();
            $table->text('emails')->nullable();

            $table->string('company_name')->nullable();
            $table->string('company_city')->nullable();
            $table->string('company_address')->nullable();
            $table->string('post_index')->nullable();
            $table->string('bank')->nullable();
            $table->string('bank_account')->nullable();
            $table->string('oked')->nullable();
            $table->string('mfo')->nullable();
            $table->string('inn')->nullable();
            $table->string('okonh')->nullable();

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
        Schema::dropIfExists('tcompanies');
    }
}
