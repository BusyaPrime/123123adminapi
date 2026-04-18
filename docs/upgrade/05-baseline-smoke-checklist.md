# Baseline Smoke Checklist

Critical flows to validate before and after each major upgrade step:

- API login
- API token verification flow
- API profile retrieval
- truck booking read/create/update critical flow
- tcar booking critical flow
- admin login
- admin users list
- admin companies list
- admin bookings list
- admin statistics/dashboard load
- tadmin login
- tadmin bookings/users/cars screens
- Excel export
- PDF generation
- file manager upload/view flow
- tracking endpoints
- scheduler boot without fatal errors
- echo/socket stack start without fatal errors

Status:

- Checklist defined.
- Local safe bootstrap completed on containerized PHP 7.4.
- `php artisan route:list` passed.
- `php artisan optimize:clear` passed.
- `php artisan config:cache` passed.
- `php artisan route:cache` passed.
- Functional app-level smoke scenarios remain pending until a local dataset is attached.
