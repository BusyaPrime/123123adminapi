<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFunnelColumnsToTrackingDailyStatsTable extends Migration
{
    public function up()
    {
        Schema::table('tracking_daily_stats', function (Blueprint $table) {
            $table->string('funnel_type', 64)->nullable()->after('user_type')->index();
            $table->string('step_name', 100)->nullable()->after('funnel_type');
            $table->unsignedTinyInteger('step_order')->default(0)->after('step_name');
            $table->unsignedBigInteger('started_count')->default(0)->after('step_order');
            $table->unsignedBigInteger('reached_count')->default(0)->after('started_count');
            $table->unsignedBigInteger('completed_count')->default(0)->after('reached_count');
            $table->unsignedBigInteger('dropped_count')->default(0)->after('completed_count');
            $table->decimal('conversion_pct', 7, 2)->default(0)->after('dropped_count');
            $table->decimal('avg_completion_minutes', 10, 2)->nullable()->after('conversion_pct');
            $table->decimal('median_completion_minutes', 10, 2)->nullable()->after('avg_completion_minutes');
            $table->unsignedBigInteger('stuck_count')->default(0)->after('median_completion_minutes');
            $table->json('meta_json')->nullable()->after('stuck_count');

            $table->index(array('stat_date', 'funnel_type'));
            $table->index(array('stat_date', 'funnel_type', 'step_name'));
            $table->index(array('funnel_type', 'step_name'));
            $table->index(array('platform', 'stat_date'));
            $table->unique(array('stat_date', 'platform', 'user_type', 'funnel_type', 'step_name'), 'tracking_daily_stats_funnel_unique');
        });
    }

    public function down()
    {
        Schema::table('tracking_daily_stats', function (Blueprint $table) {
            $table->dropUnique('tracking_daily_stats_funnel_unique');
            $table->dropIndex(array('stat_date', 'funnel_type'));
            $table->dropIndex(array('stat_date', 'funnel_type', 'step_name'));
            $table->dropIndex(array('funnel_type', 'step_name'));
            $table->dropIndex(array('platform', 'stat_date'));

            $table->dropColumn(array(
                'funnel_type',
                'step_name',
                'step_order',
                'started_count',
                'reached_count',
                'completed_count',
                'dropped_count',
                'conversion_pct',
                'avg_completion_minutes',
                'median_completion_minutes',
                'stuck_count',
                'meta_json',
            ));
        });
    }
}
