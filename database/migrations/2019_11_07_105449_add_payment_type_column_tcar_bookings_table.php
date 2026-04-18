<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPaymentTypeColumnTcarBookingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tcar_bookings', function (Blueprint $table) {
            $table->string('payment_type')->nullable()->default(null);
            $table->bigInteger('last_status_update')->nullable()->default(null);
            $table->bigInteger('waiting_time')->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tcar_bookings', function (Blueprint $table) {
            $table->dropColumn('payment_type');
            $table->dropColumn('last_status_update');
            $table->dropColumn('waiting_time');
        });
    }
}
