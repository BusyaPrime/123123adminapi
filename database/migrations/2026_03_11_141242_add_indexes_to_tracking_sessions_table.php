<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIndexesToTrackingSessionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tracking_sessions', function (Blueprint $table) {
            $table->index(['platform', 'user_type', 'created_at']);
            $table->index(['resulted_in_order', 'created_at']);
            $table->index(['ip_address', 'created_at']);
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
