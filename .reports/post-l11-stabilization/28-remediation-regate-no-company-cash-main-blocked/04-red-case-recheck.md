# 1. Цель
Перепроверить исходный красный сценарий до полного исчезновения `500` и side effect mismatch.

# 2. Что проверялось / исправлялось
- `R1` no-company cash create;
- `R2` no-company cash edit;
- `profile/show` для того же user state.

# 3. Источники / входные данные
- personal user `1748`;
- token `0f424a0e0b422e8a7b7fc9b9652e75bca702bbb0`;
- booking `4291`;
- later full-gate personal booking `4294`.

# 4. Среда выполнения
- live API `https://api.tst.casva.uz`;
- MySQL `casva`.

# 5. Команды / действия
- очищен `laravel.log`;
- отправлен `POST /truck-booking/book` c `payment_type='cash'`;
- отправлен `POST /truck-booking/book-edit/{id}`;
- отправлен `GET /profile/show`;
- снят persisted row и log tail.

# 6. Ожидаемый результат
- create/edit должны вернуться `200`;
- профиль должен вернуться `200`;
- `company_id` должен остаться `null`;
- side effect mismatch исчезает.

# 7. Фактический результат
- create `4291`: `200`;
- edit `4291`: `200`;
- `profile/show`: `200`;
- row `4291`: `payment_type=cash`, `client_company_id=null`, `company_id=null`;
- `laravel.log` без `ERROR`.

# 8. Red-path до фикса
- create `4290`: `500`, но row created;
- edit `4290`: `500`, но row updated;
- profile/show: `500`.

# 9. Green-path после фикса
- create/edit/profile теперь зеленые на том же no-company state;
- persisted state согласован с HTTP result.

# 10. Persisted DB evidence
- `4291	cash	-1	-1	...`;
- `4294	cash	-1	-1	...`;
- `-1` в SQL evidence означает `NULL`.

# 11. Logs / evidence
- после очистки лога и red-case recheck `local.ERROR` не найден;
- log tail содержит только `local.INFO` по company logging.

# 12. Ограничения
- recheck шел на test data и synthetic user;
- production telemetry не использовалась.

# 13. Статус
`GREEN`

# 14. Прямой вывод
Исходный blocker устранен: no-company cash больше не падает и не оставляет рассогласование между HTTP и БД.
