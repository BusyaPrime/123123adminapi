# 1. Цель
Устранить blocker `no-company cash -> 500 + persisted side effect`, перепроверить весь затронутый контур и дать честный re-gate verdict без касания `main`.

# 2. Что проверялось / исправлялось
- root cause chain `TruckBookingJob -> TruckBookingController -> TruckBookingResource -> UserProfileResource`;
- `no-company cash` create/edit;
- `company_id -> 0` drift на edit;
- director/employee company flow;
- spoofed `client_company_id`;
- runtime/DB/logs/diff gates;
- index rollback/reapply regate.

# 3. Источники / входные данные
- ветка `codex/v15-remediation-regate`;
- база test contour `casva`;
- isolated DB `casva_mergegate_v14`;
- synthetic users `1746/1747/1748`;
- companies `56/57`;
- problematic historical bookings `4289/4290`;
- fixed validation bookings `4291-4294`.

# 4. Среда выполнения
- локальная инженерная среда `E:\adminapi123\admin_api-safe`;
- test server `109.199.124.167`;
- live Laravel 11 contour `project_l11`;
- isolated verification copy `project_l11_mg14`.

# 5. Команды / действия
- code audit и локальный diff review;
- patch двух файлов;
- test-server sync через SFTP без push;
- `optimize:clear`, `php -l`;
- live HTTP recheck;
- SQL verification через `docker exec Casva_mysql mysql`;
- runtime/artisan regate;
- isolated `migrate:fresh`, rollback `--step=1`, reapply.

# 6. Ожидаемый результат
- `no-company cash` create/edit больше не дают `500`;
- `UserProfileResource` не падает на null company;
- `company_id -> 0` drift исчезает;
- company users не деградируют;
- logs без новых `ERROR`;
- можно открыть новый merge-gate, но не трогать `main`.

# 7. Фактический результат
- blocker устранен;
- `no-company cash` create/edit теперь `200`;
- `profile/show` для no-company теперь `200`;
- company payment director/employee осталось green;
- spoofed `client_company_id` по-прежнему игнорируется;
- bad-row count `payment_type='company' and client_company_id is null` остался `22`;
- `company_id` в create/edit больше не уходит в `0`;
- runtime/DB/logs/diff/human review gates зелёные.

# 8. Red-path до фикса
- `POST /truck-booking/book` no-company cash: `500`, но строка создавалась;
- `POST /truck-booking/book-edit/{id}` no-company cash: `500`, но строка обновлялась;
- `GET /profile/show` no-company: `500`;
- stack trace вел в `UserProfileResource.php:40`;
- edit-path затирал `company_id` в `0`.

# 9. Green-path после фикса
- booking `4291`: no-company cash create `200`;
- booking `4291`: no-company cash edit `200`;
- booking `4292`: director create/edit `200`, `client_company_id=56`;
- booking `4293`: employee create/edit `200`, `client_company_id=56`;
- booking `4294`: personal cash create/edit `200`, `client_company_id=null`, `company_id=null`;
- personal `company payment`: controlled `422 company_not_found`.

# 10. Persisted DB evidence
- `4292 -> payment_type=company, client_company_id=56, company_id=null`;
- `4293 -> payment_type=company, client_company_id=56, company_id=null`;
- `4294 -> payment_type=cash, client_company_id=null, company_id=null`;
- новых rows `payment_type='company' and client_company_id is null` нет;
- spoof company `57` в persisted rows не появилась.

# 11. Logs / evidence
- после финального business/http regate `laravel.log` содержит только `local.INFO`;
- повторяемый `local.ERROR` из `UserProfileResource.php:40` не воспроизвелся;
- runtime команды Laravel 11 / Composer прошли;
- index rollback/reapply доказан clean через isolated `--step=1`.

# 12. Ограничения
- это remediation и re-gate для test contour, не production certification;
- historical bad rows `22` не чистились;
- unrelated legacy zones вне booking/company scope не регрессионно не перепроверялись.

# 13. Статус
`GREEN`

# 14. Прямой вывод
Текущая remediation phase завершилась зелёным re-gate. Исходный blocker снят, но `main` всё равно не трогается напрямую: следующий шаг только отдельный merge-gate.
