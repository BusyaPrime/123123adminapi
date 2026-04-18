# 1. Admin smoke
Широкий authenticated GET smoke по `admin.tst.casva.uz` после фиксов прошел зелено.

Покрытые разделы:
- `/`
- `/profile`
- `/admins`
- `/admins/roles`
- `/regions`
- `/regions/distances`
- `/regions/manageTariffs`
- `/regions/changeByPrice`
- `/tracking`
- `/users`
- `/users/create`
- `/users/statistics`
- `/car-types`
- `/car-types/create`
- `/car-types/serving_prices`
- `/sizes`
- `/sizes/create`
- `/cargo-types`
- `/cargo-types/create`
- `/cancel-reasons`
- `/cancel-reasons/create`
- `/ticket-themes`
- `/ticket-themes/create`
- `/tickets`
- `/commission-rates`
- `/commission-rates/create`
- `/company_priorities`
- `/company_priorities/create`
- `/load-types`
- `/load-types/create`
- `/seasons`
- `/seasons/create`
- `/cars`
- `/cars/create`
- `/companies`
- `/companies/create`
- `/bookings`
- `/bookings/create`
- `/bookings/driver-price-offers`
- `/contacts`
- `/transactions`
- `/transactions/debts/companies`
- `/transactions/debts/users`
- `/transactions/import/form`
- `/statistics` и detail pages без required params
- `/reviews`
- `/chat`
- `/chat/bookings`
- `/articles`
- `/articles/create`
- `/appversions`
- `/appversions/create`
- `/merchanthelprequests`
- `/deleterequests`
- `/dashboards`
- `/dashboards/gmv`
- `/dashboards/funnel`

Итог:
- `66/66` checked admin routes -> `200`
- `0` new `500`

# 2. Merchant smoke
Authenticated GET smoke по `merchant.tst.casva.uz` после фиксов прошел зелено.

Покрытые разделы:
- `/`
- `/company`
- `/profile`
- `/cars`
- `/users`
- `/bookings`
- `/bookings-available`
- `/tracking`
- `/transactions`
- `/transactions/balance`
- `/statistics`
- `/reviews`
- `/chat/bookings`

Итог:
- `13/13` checked merchant routes -> `200`
- `0` new `500`

# 3. Public smoke
- `GET https://admin.tst.casva.uz/login` -> `200`
- `GET https://merchant.tst.casva.uz/login` -> `200`
- `GET https://api.tst.casva.uz/handbook/car-types` -> `200`
