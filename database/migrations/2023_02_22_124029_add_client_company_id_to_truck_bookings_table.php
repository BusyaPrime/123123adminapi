<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddClientCompanyIdToTruckBookingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('truck_bookings', 'client_company_id')) {
            Schema::table('truck_bookings', function (Blueprint $table) {
                $table->bigInteger('client_company_id')->nullable()->default(NULL)->after('company_id');
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
        Schema::table('truck_bookings', function (Blueprint $table) {
            //
        });
    }
}
