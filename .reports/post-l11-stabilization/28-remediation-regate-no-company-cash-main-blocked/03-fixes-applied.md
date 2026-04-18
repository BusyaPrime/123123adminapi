# 1. Цель
Зафиксировать точные изменения, их причины и ожидаемый эффект.

# 2. Что проверялось / исправлялось
- null-safe user/company serialization;
- `company_id` drift на edit.

# 3. Источники / входные данные
- файлы:
- `app/Domain/Users/Resources/UserProfileResource.php`
- `app/Domain/TruckBookings/Jobs/TruckBookingEditJob.php`

# 4. Среда выполнения
- локальная ветка `codex/v15-remediation-regate`;
- test server sync без push.

# 5. Команды / действия
- внесен patch через `apply_patch`;
- файлы синхронизированы на `project_l11` и `project_l11_mg14`;
- выполнены `php -l` и `optimize:clear`.

# 6. Ожидаемый результат
- убрать `500` в resource serialization;
- убрать `company_id -> 0` drift;
- не сломать director/employee company-resolution.

# 7. Фактический результат
- `UserProfileResource.php`: заменена опасная ручная логика на null-safe resolution с использованием `resolveClientCompany`, `optional(...)` и безопасным чтением `title/active/priority`;
- `TruckBookingEditJob.php`: значение `company_id` теперь сохраняется только если реально передано в request; иначе остается текущее.

# 8. Red-path до фикса
- `$_company->title` на null company;
- `company_id = request('company_id', 0)` даже когда параметр не прислан.

# 9. Green-path после фикса
- profile/show и booking resources сериализуются без падения;
- edit no longer rewrites `company_id` to `0`;
- success-path остается success-path.

# 10. Persisted DB evidence
- `4291` и `4294` после edit имеют `company_id = null`, а не `0`;
- `4292/4293` сохранили `client_company_id = 56`.

# 11. Logs / evidence
- `php -l` на обоих измененных файлах зеленый;
- после re-gate нет `local.ERROR` по `UserProfileResource`.

# 12. Ограничения
- фиксы намеренно точечные;
- notification/event architecture не рефакторилась.

# 13. Статус
`GREEN`

# 14. Прямой вывод
Примененных двух правок оказалось достаточно, чтобы снять исходный blocker и восстановить консистентность edit-path.
