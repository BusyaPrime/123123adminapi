<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddBirthdayCarLicenceUserProfiles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_profiles', function (Blueprint $table) {
            $table->string('birthday')->nullable()->default(null);
            $table->string('car_licence_number')->nullable()->default(null);
            $table->string('car_licence')->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_profiles', function (Blueprint $table) {
            $table->dropColumn('birthday');
            $table->dropColumn('car_licence_number');
            $table->dropColumn('car_licence');
        });
    }
}
