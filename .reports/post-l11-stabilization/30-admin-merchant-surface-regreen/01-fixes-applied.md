# 1. Route-generation fixes
- [index.blade.php](/E:/adminapi123/admin_api-safe/resources/views/admin/bookings/index.blade.php#L304):
  `route('admin.cars.show', ['id' => ...])` -> `route('admin.cars.show', ['car' => ...])`
- [show.blade.php](/E:/adminapi123/admin_api-safe/resources/views/admin/booking-driver-offers/show.blade.php#L282):
  `route('admin.cars.show', ['id' => ...])` -> `route('admin.cars.show', ['car' => ...])`
- [SearchStartNotification.php](/E:/adminapi123/admin_api-safe/app/Listeners/SearchStartNotification.php#L73):
  `route('admin.bookings.show', ['id' => ...])` -> `route('admin.bookings.show', ['booking' => ...])`
- [tgSendNewCorporateRequest.php](/E:/adminapi123/admin_api-safe/app/Domain/Companies/Jobs/tgSendNewCorporateRequest.php#L47):
  `route("admin.companies.show", ["id" => ...])` -> `route("admin.companies.show", ["company" => ...])`

# 2. Car-types create fix
- [create.blade.php](/E:/adminapi123/admin_api-safe/resources/views/admin/car-types/create.blade.php#L7):
  partial получает явный `carType => null`
- [_form.blade.php](/E:/adminapi123/admin_api-safe/resources/views/admin/car-types/_form.blade.php#L92):
  partial percentages стали create-safe через локальную переменную `partialPercentages`
- [_form.blade.php](/E:/adminapi123/admin_api-safe/resources/views/admin/car-types/_form.blade.php#L117):
  убран ложный reference на `$user->active`, теперь radio-state берется из `is_multi_region`

# 3. Почему это было важно
Эти баги проявлялись уже на живом Laravel 11 контуре и давали реальные `500`, несмотря на зеленый runtime/core gate. Это не теоретические замечания, а подтвержденные live blockers.
