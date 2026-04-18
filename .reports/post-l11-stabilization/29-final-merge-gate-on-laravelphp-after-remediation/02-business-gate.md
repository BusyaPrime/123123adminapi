# 1. Цель
Доказать, что ключевой business gate по `client_company_id` и no-company flows теперь ведет себя консистентно.

# 2. Baseline
- historical broken count до прогона: `22` rows c `payment_type='company' and client_company_id is null`;
- synthetic users: `1746` director, `1747` employee, `1748` personal;
- company context: valid `56`, spoof `57`.

# 3. Director create
- HTTP: `200`;
- created booking: `4295`;
- persisted row: `payment_type=company`, `client_company_id=56`, `company_id=null`;
- spoofed `client_company_id=57` не сохранился.

# 4. Employee create
- HTTP: `200`;
- created booking: `4296`;
- persisted row: `payment_type=company`, `client_company_id=56`, `company_id=null`;
- spoofed `client_company_id=57` не сохранился.

# 5. Personal cash create
- HTTP: `200`;
- created booking: `4297`;
- persisted row: `payment_type=cash`, `client_company_id=null`, `company_id=null`.

# 6. Personal company payment
- HTTP: `422`;
- body классифицирован как controlled `company_not_found`;
- persisted row не создан.

# 7. Director edit
- HTTP: `200`;
- booking `4295` успешно обновлен;
- `client_company_id` остался `56`;
- `company_id` не ушел в `0`.

# 8. Employee edit
- HTTP: `200`;
- booking `4296` успешно обновлен;
- `client_company_id` остался `56`;
- `company_id` не ушел в `0`.

# 9. Personal cash edit
- HTTP: `200`;
- booking `4297` успешно обновлен;
- `client_company_id` остался `null`;
- `company_id` остался `null`.

# 10. Итог
Business gate зеленый:
- company users пишут корректный `client_company_id`;
- personal company-payment режется до persist;
- personal cash flow не падает и не создает скрытого red-path side effect;
- spoofed `client_company_id` игнорируется.
