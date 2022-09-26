:construction: **Not ready for production.**

# Butler Health

Laravel package for returning application "health".

## Getting Started

```bash
composer require glesys/butler-health
php artisan vendor:publish --provider="Butler\Health\ServiceProvider" --tag=config
php artisan serve # visit http://localhost:8000/health
```

## Route

The default route is `/health` and is configured at `butler.health.route`.
The endpoint will return data in JSON.

```json
// example response
{
    "application": {
        "name": "application name",
        "timezone": "Europe/Stockholm",
        "php": "8.0.10",
        "laravel": "8.60.0",
        "butlerHealth": "0.1"
    },
    "checks": [
        {
            "name": "Database",
            "slug": "database",
            "group": "core",
            "description": "Check all database connections.",
            "runtimeInMilliseconds": 10,
            "result": {
                "value": 1,
                "message": "Connected to all databases.",
                "state": "ok"
            }
        }
    ]
}
```

### Custom route

Set `butler.health.route` to a falsy value to disable the default route.
Then add your own route.

```php
Route::get('/status', Butler\Health\Controller::class)->middleware('api');
```

## Checks

The package comes with some checks out of the box, see [checks](src/Checks).

Register the checks you want in `butler.health.checks`.

```php
// config/butler.php
return [
    'health' => [
        // ...
        'checks' => [
            Butler\Health\Checks\Database::class,
            App\Health\MyCheck::class,
        ],
    ],
];
```

### Create a check

Extend `Butler\Health\Check` and add it to `butler.health.checks`, done.

## Custom application data

If you want custom "application" data in the response you can register a callback like in the example below.

```php
Repository::customApplicationData(fn () => [
    'name' => 'custom name',
    'operatingSystem' => php_uname('s'),'v'),
]);
```

```json
// example response with custom application data
{
    "application": {
        "name": "custom name",
        "timezone": "Europe/Stockholm",
        "php": "8.0.10",
        "laravel": "8.60.0",
        "butlerHealth": "0.1",
        "operatingSystem": "Linux"
    },
    "checks": []
}
```

## Testing

```shell
vendor/bin/phpunit
vendor/bin/pint --test
```

## How To Contribute

Development happens at GitHub; any typical workflow using Pull Requests are welcome. In the same spirit, we use the GitHub issue tracker for all reports (regardless of the nature of the report, feature request, bugs, etc.).

All changes are supposed to be covered by unit tests, if testing is impossible or very unpractical that warrants a discussion in the comments section of the pull request.

### Code standard

As the library is intended for use in Laravel applications we encourage code standard to follow [upstream Laravel practices](https://laravel.com/docs/master/contributions#coding-style) - in short that would mean [PSR-2](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md) and [PSR-4](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-4-autoloader.md).
