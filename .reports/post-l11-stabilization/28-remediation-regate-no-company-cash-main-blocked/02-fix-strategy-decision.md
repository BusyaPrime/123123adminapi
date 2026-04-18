# 1. Цель
Зафиксировать выбранную стратегию исправления до оценки итогового результата.

# 2. Что проверялось / исправлялось
- null-dereference в resource serialization;
- edit drift `company_id -> 0`;
- риск post-commit `500` именно на известном broken path.

# 3. Источники / входные данные
- root cause chain audit;
- v15 requirements;
- фактические red-cases `4289/4290`.

# 4. Среда выполнения
- локальная инженерная среда;
- test server.

# 5. Команды / действия
- сопоставление вариантов A/B/C из ТЗ;
- оценка минимальности, риска и затрагиваемого scope.

# 6. Ожидаемый результат
Выбрать самый узкий fix, который закрывает blocker и не ломает green company paths.

# 7. Фактический результат
Выбран вариант `C`, но в минимальной форме:
- null-safe `UserProfileResource`;
- устранение `company_id -> 0` в `TruckBookingEditJob`;
- без широкого рефакторинга transaction architecture.

# 8. Red-path до фикса
- serialization crash после commit;
- edit rewrite `company_id` to `0`.

# 9. Green-path после фикса
- resource безопасно сериализует no-company user;
- edit сохраняет прежний `company_id`, если запрос его не прислал;
- business/runtime gates проходят без дополнительной перестройки доменного слоя.

# 10. Persisted DB evidence
- после стратегии C persisted rows стали согласованы с HTTP outcome;
- `company_id` перестал деградировать в `0`.

# 11. Logs / evidence
- error signature из логов исчезла;
- новые error patterns после повторной проверки не появились.

# 12. Ограничения
- transaction boundary контроллера и jobs не перестраивались;
- стратегия сознательно не трогала unrelated side effects и notification architecture.

# 13. Статус
`GREEN`

# 14. Прямой вывод
Выбранная стратегия была минимальной и достаточной: blocker снят без расползания правок по другим слоям.
