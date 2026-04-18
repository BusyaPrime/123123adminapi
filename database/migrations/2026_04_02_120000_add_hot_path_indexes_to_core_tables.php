<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->addIndexIfMissing('user_tokens', ['token'], 'user_tokens_token_idx');
        $this->addIndexIfMissing('user_profiles', ['company_id'], 'user_profiles_company_id_idx');
        $this->addIndexIfMissing('companies', ['user_id'], 'companies_user_id_idx');
        $this->addIndexIfMissing(
            'app_versions',
            ['app_type', 'userrole', 'status_id', 'is_active', 'is_deleted', 'id'],
            'app_versions_lookup_idx'
        );
        $this->addIndexIfMissing('save_address', ['user_id', 'id'], 'save_address_user_id_id_idx');
        $this->addIndexIfMissing('user_delete_requests', ['status'], 'user_delete_requests_status_idx');
        $this->addIndexIfMissing('user_delete_requests', ['user_id'], 'user_delete_requests_user_id_idx');
        $this->addIndexIfMissing(
            'transactions',
            ['company_id', 'created_at', 'id'],
            'transactions_company_created_id_idx'
        );
        $this->addIndexIfMissing(
            'transactions',
            ['user_id', 'created_at', 'id'],
            'transactions_user_created_id_idx'
        );
        $this->addIndexIfMissing(
            'truck_bookings',
            ['client_company_id', 'status', 'created_at', 'id'],
            'tb_client_company_status_created_id_idx'
        );
        $this->addIndexIfMissing(
            'truck_bookings',
            ['user_id', 'status', 'created_at', 'id'],
            'tb_user_status_created_id_idx'
        );
        $this->addIndexIfMissing(
            'truck_bookings',
            ['company_id', 'status', 'created_at', 'id'],
            'tb_company_status_created_id_idx'
        );
        $this->addIndexIfMissing(
            'truck_bookings',
            ['driver_id', 'status', 'created_at', 'id'],
            'tb_driver_status_created_id_idx'
        );
    }

    public function down(): void
    {
        $this->dropIndexIfExists('user_tokens', 'user_tokens_token_idx');
        $this->dropIndexIfExists('user_profiles', 'user_profiles_company_id_idx');
        $this->dropIndexIfExists('companies', 'companies_user_id_idx');
        $this->dropIndexIfExists('app_versions', 'app_versions_lookup_idx');
        $this->dropIndexIfExists('save_address', 'save_address_user_id_id_idx');
        $this->dropIndexIfExists('user_delete_requests', 'user_delete_requests_status_idx');
        $this->dropIndexIfExists('user_delete_requests', 'user_delete_requests_user_id_idx');
        $this->dropIndexIfExists('transactions', 'transactions_company_created_id_idx');
        $this->dropIndexIfExists('transactions', 'transactions_user_created_id_idx');
        $this->dropIndexIfExists('truck_bookings', 'tb_client_company_status_created_id_idx');
        $this->dropIndexIfExists('truck_bookings', 'tb_user_status_created_id_idx');
        $this->dropIndexIfExists('truck_bookings', 'tb_company_status_created_id_idx');
        $this->dropIndexIfExists('truck_bookings', 'tb_driver_status_created_id_idx');
    }

    private function addIndexIfMissing(string $table, array $columns, string $indexName): void
    {
        if (!$this->tableHasColumns($table, $columns)) {
            return;
        }

        if ($this->indexExists($table, $indexName) || $this->indexExistsForColumns($table, $columns)) {
            return;
        }

        Schema::table($table, function (Blueprint $table) use ($columns, $indexName) {
            $table->index($columns, $indexName);
        });
    }

    private function dropIndexIfExists(string $table, string $indexName): void
    {
        if (!Schema::hasTable($table) || !$this->indexExists($table, $indexName)) {
            return;
        }

        Schema::table($table, function (Blueprint $table) use ($indexName) {
            $table->dropIndex($indexName);
        });
    }

    private function tableHasColumns(string $table, array $columns): bool
    {
        if (!Schema::hasTable($table)) {
            return false;
        }

        foreach ($columns as $column) {
            if (!Schema::hasColumn($table, $column)) {
                return false;
            }
        }

        return true;
    }

    private function indexExists(string $table, string $indexName): bool
    {
        $indexes = $this->getTableIndexes($table);

        return array_key_exists($indexName, $indexes);
    }

    private function indexExistsForColumns(string $table, array $columns): bool
    {
        $normalizedColumns = array_values($columns);

        foreach ($this->getTableIndexes($table) as $indexColumns) {
            if ($indexColumns === $normalizedColumns) {
                return true;
            }
        }

        return false;
    }

    private function getTableIndexes(string $table): array
    {
        if (!Schema::hasTable($table)) {
            return [];
        }

        $connection = DB::connection();
        $driver = $connection->getDriverName();

        if ($driver === 'mysql') {
            $databaseName = $connection->getDatabaseName();
            $rows = $connection->select(
                'SELECT index_name, seq_in_index, column_name
                 FROM information_schema.statistics
                 WHERE table_schema = ? AND table_name = ?
                 ORDER BY index_name, seq_in_index',
                [$databaseName, $table]
            );

            $indexes = [];

            foreach ($rows as $row) {
                $indexes[$row->index_name][] = $row->column_name;
            }

            return $indexes;
        }

        if ($driver === 'sqlite') {
            $indexList = $connection->select("PRAGMA index_list('{$table}')");
            $indexes = [];

            foreach ($indexList as $index) {
                $indexName = $index->name ?? null;

                if (!$indexName) {
                    continue;
                }

                $indexInfo = $connection->select("PRAGMA index_info('{$indexName}')");
                $indexes[$indexName] = array_values(array_map(
                    static fn ($row) => $row->name,
                    $indexInfo
                ));
            }

            return $indexes;
        }

        return [];
    }
};
