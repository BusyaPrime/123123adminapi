<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDataCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->bigInteger('user_id')->nullable()->default(null);
            $table->boolean('active')->nullable()->default(null);
            $table->string('director_name')->nullable()->default(null);
            $table->string('licence')->nullable()->default(null);
            $table->string('agreement')->nullable()->default(null);
            $table->string('certificate')->nullable()->default(null);
            $table->string('rating')->nullable()->default(null);
            $table->double('commission', 5, 2)->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn('user_id');
            $table->dropColumn('active');
            $table->dropColumn('director_name');
            $table->dropColumn('licence');
            $table->dropColumn('agreement');
            $table->dropColumn('certificate');
            $table->dropColumn('rating');
            $table->dropColumn('commission');
        });
    }
}
