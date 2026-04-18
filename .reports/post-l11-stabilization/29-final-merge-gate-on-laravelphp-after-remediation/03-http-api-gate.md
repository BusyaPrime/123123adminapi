# 1. Цель
Подтвердить wire-level HTTP-поведение после remediation на test contour.

# 2. Public contour smoke
- `GET https://admin.tst.casva.uz/login` -> `200`
- `GET https://merchant.tst.casva.uz/login` -> `200`
- `GET https://api.tst.casva.uz/handbook/car-types` -> `200`

# 3. Authenticated API gate
- director `POST /truck-booking/book` -> `200`
- employee `POST /truck-booking/book` -> `200`
- personal cash `POST /truck-booking/book` -> `200`
- personal company `POST /truck-booking/book` -> `422`
- director `POST /truck-booking/book-edit/{id}` -> `200`
- employee `POST /truck-booking/book-edit/{id}` -> `200`
- personal cash `POST /truck-booking/book-edit/{id}` -> `200`

# 4. Profile / company gate
- director `GET /profile/show` -> `200`
- director `GET /client-company/show` -> `200`
- director `GET /client-company/bookings?status=order` -> `200`
- employee `GET /profile/show` -> `200`
- employee `GET /client-company/show` -> `200`
- employee `GET /client-company/bookings?status=order` -> `200`
- personal `GET /profile/show` -> `200`
- personal `GET /client-company/show` -> `404`
- personal `GET /client-company/bookings?status=order` -> `404`

# 5. Что важно
- исходный `500` на no-company cash path больше не воспроизводится;
- исходный `500` на `profile/show` для no-company user больше не воспроизводится;
- controlled negative cases остаются controlled и не маскируются под `200`.

# 6. Итог
HTTP gate зеленый.
