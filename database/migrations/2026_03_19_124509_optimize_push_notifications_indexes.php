<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class OptimizePushNotificationsIndexes extends Migration
{
    public function up()
    {
        $this->dropIndexIfExists('idx_id');
        $this->dropIndexIfExists('idx_read');
        $this->dropIndexIfExists('idx_user_id');

        Schema::table('push_notifications', function (Blueprint $table) {
            $table->index(['user_id', 'read', 'created_at'], 'idx_user_read_created');
            $table->index('created_at', 'idx_created_at');
        });
    }

    public function down()
    {
        $this->dropIndexIfExists('idx_user_read_created');
        $this->dropIndexIfExists('idx_created_at');

        Schema::table('push_notifications', function (Blueprint $table) {
            $table->index('user_id', 'idx_user_id');
            $table->index('read', 'idx_read');
            $table->index('id', 'idx_id');
        });
    }

    private function dropIndexIfExists(string $indexName): void
    {
        $exists = DB::selectOne("
            SELECT 1 FROM information_schema.STATISTICS
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME = 'push_notifications'
              AND INDEX_NAME = ?
        ", [$indexName]);

        if ($exists) {
            DB::statement("ALTER TABLE push_notifications DROP INDEX `{$indexName}`");
        }
    }
}