# 1. Цель
Подтвердить напрямую в БД, что persist после фикса соответствует бизнес-правилу и больше не расходится с HTTP.

# 2. Что проверялось / исправлялось
- `client_company_id`;
- `company_id`;
- `payment_type`;
- accidental writes на red path;
- spoof persistence;
- bad-row count.

# 3. Источники / входные данные
- bookings `4292-4294`;
- historical count `22` для company rows with null `client_company_id`.

# 4. Среда выполнения
- MySQL `casva` внутри `Casva_mysql`.

# 5. Команды / действия
- прямой `SELECT` по booking ids;
- count query по `payment_type='company' AND client_company_id IS NULL`;
- select по red comment;
- compare before/after.

# 6. Ожидаемый результат
- company bookings пишут `client_company_id=56`;
- cash bookings не получают company binding;
- `company_id` не деградирует в `0`;
- no broken row on personal company-payment red.

# 7. Фактический результат
- `4292`: `company / 56 / null`;
- `4293`: `company / 56 / null`;
- `4294`: `cash / null / null`;
- `personal company red`: `0` rows;
- bad-row count до/после: `22 -> 22`.

# 8. Red-path до фикса
- `4290` существовал как `cash / null / 0` после error path;
- error response не отменял persisted write.

# 9. Green-path после фикса
- success-path и persisted DB state совпадают;
- red path не создает broken row;
- drift `company_id=0` не воспроизводится.

# 10. Persisted DB evidence
- SQL evidence:
- `4292  company  56  null`
- `4293  company  56  null`
- `4294  cash  null  null`
- `COUNT(company-null bad rows)=22`

# 11. Logs / evidence
- data gate сопровождался чистым логом без ошибок;
- новых SQL/runtime ошибок не зафиксировано.

# 12. Ограничения
- historical rows `22` остаются legacy debt и не входят в fix scope.

# 13. Статус
`GREEN`

# 14. Прямой вывод
Data gate зеленый: persist корректен, spoof не влияет, новых broken rows после фикса не появляется.
