# 1. Цель
Вернуть успешный remediation diff в чистую ветку `LARAVELPHP`, заново открыть финальный merge-gate и дать бинарное решение, можно ли технически обсуждать движение дальше без касания `main`.

# 2. Что проверялось
- freeze и чистота ветки `LARAVELPHP`;
- перенос remediation diff из `codex/v15-remediation-regate`;
- business gate по `client_company_id`;
- HTTP gate по client/company/profile paths;
- data gate по persisted rows;
- runtime / DB / index / logs / diff gate;
- human review gate.

# 3. Среда выполнения
- локальная ветка: `LARAVELPHP`;
- локальный HEAD: `163de687ca72fac8663da08c3a98b788f3ca9441`;
- worktree: clean;
- test server: `109.199.124.167`;
- live Laravel 11 contour: `/var/www/casva/backend/project_l11`;
- isolated DB contour: `casva_mergegate_v14`.

# 4. Что было взято в merge-gate
- remediation fix по `UserProfileResource` null-path;
- remediation fix по `TruckBookingEditJob` drift `company_id -> 0`;
- уже закрытый фикс по `client_company_id` resolution для director/employee;
- index migration `2026_04_02_120000_add_hot_path_indexes_to_core_tables.php`.

# 5. Что подтверждено
- `client_company_id` для director и employee сохраняется корректно;
- spoofed `client_company_id` не влияет на persist;
- personal `company payment` режется контролируемым `422 company_not_found`;
- personal `cash` create/edit больше не дают `500`;
- side effect при red-path не воспроизводится;
- `profile/show` для no-company user теперь `200`;
- `company_id -> 0` drift на edit устранен;
- runtime / DB / index rollback-reapply / logs / diff gates зеленые.

# 6. Что не делалось
- `main` не трогался;
- push не выполнялся;
- production deployment не выполнялся;
- historical broken rows не чистились.

# 7. Итоговый статус
`MERGE-GATE PASSED`

# 8. Прямой вывод
Технический merge-gate на `LARAVELPHP` после remediation пройден. Это снимает блокировку именно на уровне engineering gate и позволяет отдельно обсуждать следующий шаг. Сам `main` по итогам этой фазы не изменялся и не пушился.
