# 1. Цель
Подтвердить, что persisted DB state совпадает с HTTP-результатом и не содержит новых broken rows.

# 2. Контрольные persisted rows
- booking `4295` -> `user_id=1746`, `payment_type=company`, `client_company_id=56`, `company_id=null`
- booking `4296` -> `user_id=1747`, `payment_type=company`, `client_company_id=56`, `company_id=null`
- booking `4297` -> `user_id=1748`, `payment_type=cash`, `client_company_id=null`, `company_id=null`

# 3. Anti-spoof proof
- тестовый payload с `client_company_id=57` не повлиял на persisted rows;
- фактически в company bookings сохранился `56`, вычисленный сервером из auth user.

# 4. Broken-row count
- count до финального merge-gate: `22`
- count после финального merge-gate: `22`
- новых строк `payment_type='company' and client_company_id is null` не появилось.

# 5. Red-path side effect check
- personal company-payment дал controlled `422`;
- row для этого кейса не появилась;
- прежний дефект вида `HTTP 500, но строка уже создана/обновлена` не воспроизведен.

# 6. Edit drift check
- `company_id -> 0` после edit больше не воспроизводится;
- на `4295`, `4296`, `4297` persisted `company_id` остался `null`.

# 7. Итог
Data gate зеленый. Persisted state соответствует ожидаемому business contract.
