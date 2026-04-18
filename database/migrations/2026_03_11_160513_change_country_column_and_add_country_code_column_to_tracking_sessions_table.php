<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeCountryColumnAndAddCountryCodeColumnToTrackingSessionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tracking_sessions', function (Blueprint $table) {
            $table->string('country', 255)->nullable()->change();
            $table->string('country_code', 2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tracking_sessions', function (Blueprint $table) {
            //
        });
    }
}
