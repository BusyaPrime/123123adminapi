<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AppVersions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('app_versions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('version_no')->nullable();
            $table->string('app_type')->nullable();
            $table->bigInteger('status_id')->nullable();
            $table->timestamp('depricated_date')->nullable()->default(null);
            $table->bigInteger('is_active')->nullable()->default('1');
            $table->bigInteger('is_deleted')->nullable()->default('0');
            $table->timestamp('created_date_time')->nullable()->default(\DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_date_time')->nullable()->default(\DB::raw('CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP'));
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
        Schema::dropIfExists('app_versions');
    }
}
