<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Constants\TrackingOrderOutcome;

class CreateTrackingOrderFunnelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tracking_order_funnels', function (Blueprint $table) {
            $table->bigIncrements('id');

            //Session
            $table->unsignedBigInteger('session_id')->index();
            $table->foreign('session_id')->references('id')->on('tracking_sessions')->onDelete('cascade');
            
            //User
            $table->unsignedBigInteger('user_id')->index();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            
            //Booking
            $table->unsignedBigInteger('order_id')->index();
            $table->foreign('order_id')->references('id')->on('truck_bookings')->onDelete('cascade');
            
            //Addresses
            $table->string('from_address')->nullable();
            $table->string('to_address')->nullable();
            
            //Price
            $table->unsignedBigInteger('calculated_price')->nullable();
            
            //Car type
            $table->unsignedBigInteger('car_type_id')->nullable();
            $table->foreign('car_type_id')->references('id')->on('car_types')->onDelete('cascade');
            
            //Cargo type
            $table->unsignedBigInteger('cargo_type_id')->nullable();
            $table->foreign('cargo_type_id')->references('id')->on('cargo_types')->onDelete('cascade');

            //Weight
            $table->integer('cargo_weight')->nullable();

            //Steps
            $table->tinyInteger('max_step_reached')->default(false);
            $table->integer('total_steps')->nullable();

            //Detailed steps data
            $table->json('steps_data')->nullable();

            //Outcome
            $table->enum('outcome', TrackingOrderOutcome::cases())->nullable()->default(TrackingOrderOutcome::IN_PROGRESS);

            //Name of step where the user abandoned
            $table->string('abandoned_at_step_name')->nullable();

            //end timestamp
            $table->timestamp('ended_at')->nullable();
            
            //abandoned timestamp
            $table->timestamp('abandoned_at')->nullable();
            
            //Total spent time
            $table->timestamp('total_time_seconds')->nullable();

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
        Schema::dropIfExists('tracking_order_funnels');
    }
}
