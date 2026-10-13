# Project Instructions

## Scope
- This is a Laravel 11 application using PHP 8.2+, Jetstream, Sanctum, Livewire, Dompdf, and Laravel Excel.
- Keep authorization changes server-side and preserve existing admin/superadmin behavior.
- Do not edit `.env`, secrets, migrations, or production data unless explicitly requested.

## Commands
- Install dependencies: `composer install`
- Check PHP syntax: `php -l path/to/file.php`
- Run focused tests: `php artisan test --compact tests/Feature/ExampleTest.php`
- Run the full suite: `php artisan test --compact`
- Format PHP only when requested: `./vendor/bin/pint`

## Conventions
- Follow existing Laravel controller, route, middleware, model, Blade, and PHPUnit conventions.
- Use policies/middleware or controller authorization checks for access control; validate route-model-bound resources before exposing data.
- Keep tests focused in `tests/Feature` and use factories/seeders already present.
- Return Laravel responses consistent with neighboring code; avoid leaking PII in unauthorized responses.

## Boundaries
- Make the smallest safe change needed for the requested behavior.
- Do not weaken authorization to make tests pass.
- Do not run destructive database commands or modify production data.

## Troubleshooting
- If tests fail during setup, inspect `.env.testing`, database configuration, migrations, and test factories before changing application code.
- If a route behaves unexpectedly, inspect route middleware and `php artisan route:list` before changing controllers.
- Report failed commands and their relevant output exactly.
