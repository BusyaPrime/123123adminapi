# Baseline Benchmarks

Status: initial baseline captured under containerized PHP 7.4.

Measured commands:

- `composer install --no-scripts --prefer-dist`: completed successfully during baseline bootstrap
- `composer dump-autoload --no-scripts`: `24.76s`
- `php artisan optimize:clear`
- `php artisan config:cache`
- `php artisan route:cache` if compatible
- `php artisan optimize:clear`: `7.23s`
- `php artisan config:cache`: `4.57s`
- `php artisan route:cache`: `5.16s`

Observed notes:

- Baseline runtime confirmed on `PHP 7.4.33` + `Laravel 5.8.38`.
- `route:list` works under the local safe env and route surface was dumped to `01-baseline-routes.txt`.
- Composer autoload reports PSR-4 violations that must be cleaned before later major upgrades.
- Page/API response benchmarks are still pending because no local application data set has been mounted yet.
- Database and query-level benchmarks are still pending because no local dump has been attached yet.
