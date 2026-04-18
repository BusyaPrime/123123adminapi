# Baseline Environment

Date: 2026-04-01

## Workspace

- Project root: `E:\adminapi123\admin_api-main`
- Migration branch: `local/upgrade-laravel11-safe`
- Git remote: none

## Host tools

- Git: `2.53.0.windows.1`
- Node: `v24.13.1`
- npm: `11.8.0`
- Host PHP on PATH: missing
- Host Composer on PATH: missing
- Host fallback PHP found: `C:\xampp\php\php.exe`
- Host fallback PHP version: `8.2.12`

## Container tools

- Docker Desktop server: `29.2.0`
- PHP 7.4 baseline container: available
- Composer 2.2 container image: available
- PHP 8.3 target container: pending local build

## Current project state before upgrade

- Laravel target in composer: `5.8.*`
- PHP constraint in composer: `^7.4.3`
- `vendor/`: installed under PHP 7.4 baseline container
- `.env.example`: missing
- `phpunit.xml`: missing
- Original repository metadata: missing, project unpacked without `.git`

## Constraints observed at baseline

- The project is not runnable on host as-is because host `php` and `composer` are absent from PATH.
- Baseline dependency install was completed inside a controlled PHP 7.4 container using `composer install --no-scripts`.
- Dynamic Artisan baseline is now working under the containerized baseline stack.

## Confirmed runtime baseline

- PHP baseline runtime: `7.4.33`
- Laravel baseline runtime: `5.8.38`
- Locked packages installed: `133`
- Dynamic route list captured: `424` route rows from `php artisan route:list`

## First upgrade blockers already observed

- Abandoned packages: `dimsav/laravel-translatable`, `swiftmailer/swiftmailer`, `symfony/debug`, `doctrine/cache`, `fzaninotto/faker`, `phpunit/php-token-stream`
- PSR-4 autoload anomalies in several domain files, including mislocated classes under `app/Domain/AppVersions/*`, `app/Domain/Tcompanies/*`, `app/Domain/TrackingEvent/*`, `app/Domain/UserDeleteRequests/*`
