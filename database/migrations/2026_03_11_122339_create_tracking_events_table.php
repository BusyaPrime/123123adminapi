<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTrackingEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tracking_events', function (Blueprint $table) {
            $table->bigIncrements('id');

            //Session
            $table->unsignedBigInteger('session_id')->index();
            $table->foreign('session_id')->references('id')->on('tracking_sessions')->onDelete('cascade');
            
            //User
            $table->unsignedBigInteger('user_id')->index();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            /* 
             * Event types: 
             * 
             * Navigation: 
             * page_view, page_exit, app_open, app_background, app_close 
             * 
             * Prices: 
             * price_calculator_open, price_calculated, price_viewed 
             * 
             * Order: 
             * order_start, order_step_complete, order_step_back, 
             * order_abandoned, order_created, order_paid, order_cancelled 
             * 
             *Account: 
             * registration_start, registration_step, registration_complete, 
             * registration_abandoned, login_attempt, login_success, login_failed 
             * 
             * Interaction: 
             * button_click, form_focus, form_error, search_performed, 
             * filter_applied, map_interaction, phone_tap, scroll_depth 
             * 
             * Bugs: 
             * error_shown, api_error 
             */
            $table->string('event_type', 100)->nullable();

            //Screen name
            $table->string('screen_name', 150)->nullable();

            // JSON event properties - flexible container
            $table->json('properties')->nullable();

            //Funnel step number if exists
            $table->tinyInteger('funnel_step')->nullable();

            //Funnel step name
            $table->string('funnel_name', 150)->nullable();

            //Order id, if created
            $table->unsignedBigInteger('order_id')->nullable()->index();
            $table->foreign('order_id')->references('id')->on('truck_bookings')->onDelete('cascade');

            //Calculated price, if order created order price, if only calculated, calculated price
            $table->unsignedBigInteger('value')->nullable();
            
            //Duration of times since event start
            $table->unsignedBigInteger('time_since_session_start')->nullable();

            $table->timestamps(); //session_id, user_id, event_type, screen_name, properties, funnel_step, funnel_name, order_id, value, time_since_session_start
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tracking_events');
    }
}
