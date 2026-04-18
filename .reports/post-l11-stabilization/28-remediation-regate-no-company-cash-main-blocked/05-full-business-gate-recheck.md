# 1. Цель
Повторно прогнать весь business gate после фикса, а не только исходный red-case.

# 2. Что проверялось / исправлялось
- B1 director create;
- B2 employee create;
- B3 personal cash create;
- B4 personal company-payment red;
- B5 spoofed `client_company_id`;
- B6 director edit;
- B7 employee edit;
- B8 company/profile endpoints after order.

# 3. Источники / входные данные
- bookings `4292`, `4293`, `4294`;
- red comment `V15 personal company red 1775147864`;
- company `56`;
- spoof company `57`.

# 4. Среда выполнения
- live test contour;
- DB `casva`.

# 5. Команды / действия
- реальные `POST /truck-booking/book`;
- реальные `POST /truck-booking/book-edit/{id}`;
- `GET /profile/show`;
- `GET /client-company/show`;
- `GET /client-company/bookings?status=order`;
- SQL verification after each branch.

# 6. Ожидаемый результат
- director/employee company paths green;
- personal cash green;
- personal company-payment controlled `422`;
- spoofed input ignored;
- company/profile endpoints without `500`.

# 7. Фактический результат
- director create/edit: `200`, `client_company_id=56`;
- employee create/edit: `200`, `client_company_id=56`;
- personal cash create/edit: `200`, `client_company_id=null`, `company_id=null`;
- personal company-payment: `422 company_not_found`, row not created;
- director/employee `profile/show`, `client-company/show`, `client-company/bookings`: `200`;
- personal `profile/show`: `200`;
- personal `client-company/*`: controlled `404 company_not_found`.

# 8. Red-path до фикса
- personal cash create/edit/profile были `500`;
- edit-path создавал drift `company_id -> 0`.

# 9. Green-path после фикса
- все обязательные business cases выполняются по контракту;
- controlled red остался controlled, success-path стал green.

# 10. Persisted DB evidence
- `4292 -> company, 56, null`;
- `4293 -> company, 56, null`;
- `4294 -> cash, null, null`;
- row для `personal company red` отсутствует.

# 11. Logs / evidence
- `local.ERROR` по итогам полного business gate отсутствует;
- лог содержит только ожидаемые `local.INFO`.

# 12. Ограничения
- прогон ограничен scope `client_company_id / no-company cash / profile/company endpoints`;
- полный cross-module regression не выполнялся.

# 13. Статус
`GREEN`

# 14. Прямой вывод
Полный business gate после фикса зеленый: известных регрессий в целевом контуре не осталось.
