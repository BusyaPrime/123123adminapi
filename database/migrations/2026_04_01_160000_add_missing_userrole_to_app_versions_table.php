<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasColumn('app_versions', 'userrole')) {
            Schema::table('app_versions', function (Blueprint $table) {
                $table->string('userrole')->nullable()->after('app_type');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('app_versions', 'userrole')) {
            Schema::table('app_versions', function (Blueprint $table) {
                $table->dropColumn('userrole');
            });
        }
    }
};
