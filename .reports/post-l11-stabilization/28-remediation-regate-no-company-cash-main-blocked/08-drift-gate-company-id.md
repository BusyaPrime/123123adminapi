# 1. Цель
Отдельно доказать, что drift `company_id -> 0` устранен и create/edit снова согласованы.

# 2. Что проверялось / исправлялось
- edit assignment в `TruckBookingEditJob`;
- persisted `company_id` before/after edit;
- director / employee / personal paths.

# 3. Источники / входные данные
- old broken examples `4287-4290`;
- fixed examples `4292-4294`.

# 4. Среда выполнения
- code audit;
- live DB verification.

# 5. Команды / действия
- проверен код `TruckBookingEditJob`;
- выполнены fresh edit requests;
- сняты persisted rows после edit.

# 6. Ожидаемый результат
- при отсутствии `company_id` в request значение не должно превращаться в `0`;
- edit должен сохранять текущий `company_id`.

# 7. Фактический результат
- код больше не использует `input('company_id', 0)`;
- director edit `4292`: `company_id=null`;
- employee edit `4293`: `company_id=null`;
- personal edit `4294`: `company_id=null`.

# 8. Red-path до фикса
- `4287`, `4288`, `4289`, `4290` после edit имели `company_id=0`.

# 9. Green-path после фикса
- `4292-4294` после edit сохраняют `company_id=null`;
- create/edit consistency восстановлена.

# 10. Persisted DB evidence
- `4292	company	56	-1`;
- `4293	company	56	-1`;
- `4294	cash	-1	-1`;
- `-1` в evidence означает `NULL`.

# 11. Logs / evidence
- drift fix не породил новых runtime/log anomalies.

# 12. Ограничения
- проверка шла по representative edit cases, не по всем возможным write paths проекта.

# 13. Статус
`GREEN`

# 14. Прямой вывод
Drift gate зеленый: `company_id -> 0` устранен.
