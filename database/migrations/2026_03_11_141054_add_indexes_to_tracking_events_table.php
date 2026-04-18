<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIndexesToTrackingEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tracking_events', function (Blueprint $table) {
            $table->index(['screen_name', 'event_type']);
            $table->index(['funnel_name', 'funnel_step']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tracking_events', function (Blueprint $table) {
            //
        });
    }
}
