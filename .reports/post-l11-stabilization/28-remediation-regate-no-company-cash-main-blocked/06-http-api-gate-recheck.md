# 1. Цель
Переподтвердить HTTP-контракты после remediation.

# 2. Что проверялось / исправлялось
- create booking директором;
- create booking сотрудником;
- create booking personal cash;
- create booking personal company payment;
- edit booking директором;
- edit booking сотрудником;
- edit booking personal cash;
- profile/company endpoints.

# 3. Источники / входные данные
- live API `https://api.tst.casva.uz`;
- payloadы из полного business regate;
- tokens director/employee/personal.

# 4. Среда выполнения
- deployed Laravel 11 test contour.

# 5. Команды / действия
- прямые HTTP requests с bearer tokens;
- body/status capture;
- сверка с persisted DB state.

# 6. Ожидаемый результат
- success-path дает `200`;
- personal company-payment дает controlled `422`;
- no-company company endpoints дают controlled `404`;
- новых `500` нет.

# 7. Фактический результат
- `POST /truck-booking/book` director: `200`;
- `POST /truck-booking/book` employee: `200`;
- `POST /truck-booking/book` personal cash: `200`;
- `POST /truck-booking/book` personal company-payment: `422`;
- `POST /truck-booking/book-edit/{id}` director/employee/personal: `200`;
- `GET /profile/show` director/employee/personal: `200`;
- `GET /client-company/show` director/employee: `200`, personal: `404`;
- `GET /client-company/bookings?status=order` director/employee: `200`, personal: `404`.

# 8. Red-path до фикса
- `book` personal cash: `500`;
- `book-edit` personal cash: `500`;
- `profile/show` personal: `500`.

# 9. Green-path после фикса
- все целевые HTTP контракты выполняются;
- controlled red больше не превращается в crash.

# 10. Persisted DB evidence
- каждый `200` success-path имеет соответствующий persisted row;
- controlled `422/404` не производит broken state.

# 11. Logs / evidence
- no `local.ERROR` после HTTP regate;
- response bodies соответствуют ожиданию.

# 12. Ограничения
- только representative scope, не весь API surface.

# 13. Статус
`GREEN`

# 14. Прямой вывод
HTTP API gate после remediation зеленый: аварийного no-company behavior больше нет.
