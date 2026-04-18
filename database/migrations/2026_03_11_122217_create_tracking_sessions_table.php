<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Constants\UserType;
use App\Constants\PlatformType;
use App\Constants\TrackingSessionStatus;

class CreateTrackingSessionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tracking_sessions', function (Blueprint $table) {
            $table->bigIncrements('id');

            //Unique session token created in client side
            $table->string('session_token', 64)->unique();

            //User's ID If user has logged in
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');

            //Type of user anonym or registered
            $table->enum('user_type', UserType::cases())->nullable()->default(UserType::ANONYMOUS);
            
            //Platform name, web, ios, android...
            $table->enum('platform', PlatformType::cases())->nullable()->index();

            //version of app
            $table->string('app_version', 255)->nullable();
            
            //IP Address of user
            $table->string('ip_address', 45)->nullable()->index();

            //User agent, if WEB
            $table->text('user_agent', 255)->nullable();

            //Name of browser if WEB
            $table->string('browser', 255)->nullable();

            //Version of browser if WEB
            $table->string('browser_version', 255)->nullable();

            //Operating system
            $table->string('os', 255)->nullable();

            //Device type, desktop, mobile, tablet
            $table->string('device_type', 255)->nullable();

            //Url of previous page, if exists
            $table->string('referrer_url', 255)->nullable();

            //Source of traffic
            $table->string('utm_source', 255)->nullable();

            //Type of traffic
            $table->string('utm_medium', 255)->nullable();

            //Name of ad company
            $table->string('utm_campaign', 255)->nullable();

            //Unique ID of device (only mobile). Allows to identify device even if user has reinstalled app
            $table->string('device_id', 255)->nullable();

            //Model of device
            $table->string('device_model', 255)->nullable();

            //Country, calculated from IP address
            $table->string('country', 2)->nullable();

            //City calculated from IP address
            $table->string('city', 255)->nullable();
            
            //LatLong calculated from IP address
            $table->decimal('lat', 10, 7)->nullable();
            $table->decimal('lng', 10, 7)->nullable();

            //Languga of application
            $table->string('language', 2)->nullable();

            //Timezone of user
            $table->string('timezone')->nullable();

            //status of session, active — the session is open, completed — closed normally, abandoned — abandoned without closing
            $table->enum('status', TrackingSessionStatus::cases())->nullable()->index()->default(TrackingSessionStatus::ACTIVE);

            //Has user created order in this session
            $table->tinyInteger('resulted_in_order')->default(false);
            
            //Order ID if maked order
            $table->unsignedBigInteger('order_id')->nullable()->index();
            $table->foreign('order_id')->references('id')->on('truck_bookings')->onDelete('cascade');

            //End timstamp of the session
            $table->timestamp('ended_at')->nullable();

            //Total duration of session
            $table->bigInteger('duration_seconds')->nullable();

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
        Schema::dropIfExists('tracking_sessions');
    }
}
