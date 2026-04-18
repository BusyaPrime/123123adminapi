# 1. Цель
Полностью расписать цепочку, из-за которой `no-company cash` давал `500` уже после успешного persist.

# 2. Что проверялось / исправлялось
- `TruckBookingJob.php`;
- `TruckBookingEditJob.php`;
- `TruckBookingController.php`;
- `TruckBookingResource.php`;
- `UserProfileResource.php`.

# 3. Источники / входные данные
- код ветки `codex/v15-remediation-regate`;
- live failures bookings `4289/4290`;
- stack trace из `laravel.log`.

# 4. Среда выполнения
- локальный code audit;
- live test contour `project_l11`.

# 5. Команды / действия
- line audit файлов;
- воспроизведение `book`, `book-edit`, `profile/show`;
- SQL verification persisted rows;
- `tail`/`grep` по `laravel.log`.

# 6. Ожидаемый результат
Найти не только строку null-dereference, но весь порядок операций: persist, response assembly, serialization, crash.

# 7. Фактический результат
- `TruckBookingJob` и `TruckBookingEditJob` делают `DB::beginTransaction`, `save`, `commit`, затем возвращают booking;
- `TruckBookingController` после этого собирает `TruckBookingResource`;
- `TruckBookingResource` сериализует `user`/`driver` через `UserProfileResource`;
- `UserProfileResource` на no-company external user делал `$_company->title` без null-check;
- исключение возникало уже после DB commit.

# 8. Red-path до фикса
- create cash: `TruckBookingJob` коммитил booking, потом `TruckBookingResource` падал на `UserProfileResource`;
- edit cash: `TruckBookingEditJob` коммитил update, потом тот же serialization path падал;
- profile endpoint падал тем же null-dereference без участия booking resource.

# 9. Green-path после фикса
- `UserProfileResource` больше не дергает nullable company без проверки;
- controller serialization проходит;
- no-company user возвращает безопасный профиль/booking payload;
- create/edit success path больше не превращается в post-commit `500`.

# 10. Persisted DB evidence
- до фикса: `4290` создавался и потом обновлялся при `500`;
- после фикса: `4291` и `4294` создаются/редактируются уже в success-path, side-effect соответствует HTTP.

# 11. Logs / evidence
- до фикса: `Attempt to read property "title" on null` в `UserProfileResource.php:40`;
- после фикса: повторяемый `local.ERROR` отсутствует;
- подтверждено прямым recheck `profile/show` и booking create/edit.

# 12. Ограничения
- chain audit целенаправленно шел по проблемному контуру и не претендует на полный архитектурный обзор всех ресурсов проекта.

# 13. Статус
`GREEN`

# 14. Прямой вывод
Root cause chain полностью локализован: проблема была не в `client_company_id`, а в post-persist serialization через `UserProfileResource`, плюс отдельный drift в edit job.
