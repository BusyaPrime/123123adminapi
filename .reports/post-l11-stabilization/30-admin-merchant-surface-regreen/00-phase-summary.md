# 1. Цель
Закрыть новый live blocker на `admin/bookings`, добрать соседние route-generation ошибки, повторно прогнать широкий admin/merchant/public smoke и вернуть tested surface в зеленое состояние без касания `main`.

# 2. Что было сломано
- `admin/bookings` падал в `500` из-за `route('admin.cars.show', ['id' => ...])` при маршруте `cars/show/{car}`;
- такой же defect был в `admin/bookings/driver-price-offers`;
- тот же класс ошибки был в Telegram link generation для `admin.bookings.show` и `admin.companies.show`;
- после расширенного smoke нашелся еще один independent blocker: `admin/car-types/create` падал из-за `Undefined variable $carType` в общем partial.

# 3. Что исправлено
- route param `id -> car` в:
  - [index.blade.php](/E:/adminapi123/admin_api-safe/resources/views/admin/bookings/index.blade.php)
  - [show.blade.php](/E:/adminapi123/admin_api-safe/resources/views/admin/booking-driver-offers/show.blade.php)
- route param `id -> booking` в:
  - [SearchStartNotification.php](/E:/adminapi123/admin_api-safe/app/Listeners/SearchStartNotification.php)
- route param `id -> company` в:
  - [tgSendNewCorporateRequest.php](/E:/adminapi123/admin_api-safe/app/Domain/Companies/Jobs/tgSendNewCorporateRequest.php)
- create/edit-safe rendering для car-types form в:
  - [create.blade.php](/E:/adminapi123/admin_api-safe/resources/views/admin/car-types/create.blade.php)
  - [_form.blade.php](/E:/adminapi123/admin_api-safe/resources/views/admin/car-types/_form.blade.php)

# 4. Что проверено
- live test contour `project_l11`;
- mirrored verification copy `project_l11_mg14`;
- admin authenticated GET smoke;
- merchant authenticated GET smoke;
- public login/API smoke;
- post-fix `laravel.log` after canonical smoke.

# 5. Итог
`GREEN IN TESTED SCOPE`

# 6. Прямой вывод
На проверенном surface новые live blockers устранены: `admin`, `merchant` и public contour в рамках канонического smoke зеленые, свежий `laravel.log` после прогона пустой, временные smoke-users удалены.
