<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Constants\PlatformType;
use App\Constants\UserType;

class CreateTrackingDailyStatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tracking_daily_stats', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->timestamp('stat_date')->nullable()->index();
            $table->enum('platform', [...PlatformType::cases(), 'all'])->index();
            $table->enum('user_type', [...UserType::cases(), 'all'])->index();

            //Count of sessions per day
            $table->bigInteger('total_sessions');

            //Count of unique IP addresses
            $table->bigInteger('unique_ips');

            //Count of unique devices
            $table->bigInteger('unique_devices');
            
            //Count of calculated prices
            $table->bigInteger('sessions_with_price_view');
            
            //Count of order create starts
            $table->bigInteger('sessions_with_order_start');
            
            //Count of order completed (created)
            $table->bigInteger('sessions_with_order_complete');
            
            //Count of order abandoned (started but not completed)
            $table->bigInteger('sessions_abandoned_at_order');
            
            //Average session time in seconds
            $table->bigInteger('avg_session_duration_seconds');

            //Average duration of order start and order created (end of create). Spen time to create order
            $table->bigInteger('avg_order_completion_seconds');

            //Sum of calculated prices of daily orders
            $table->bigInteger('total_revenue');

            //Daily orders count
            $table->bigInteger('total_orders');

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
        Schema::dropIfExists('tracking_daily_stats');
    }
}
