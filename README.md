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
{
    "about": {
        "environment": {},
        "cache": {},
        "drivers": {},
        "butler_health": {
            "version": "0.1"
        },
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

## Custom about information

You can push additional "about" information.

```php
Butler\Health\Repository::add('environment', ['operating_system' => php_uname('s')]);

Butler\Health\Repository::add('environment', fn () => ['time' => time()]);
```

```json
{
    "about": {
        "environment": {
            "operating_system": "Linux",
            "time": 1678100209
        },
        "cache": {},
        "drivers": {},
        "butler_health": {},
    },
    "checks": []
}
```

## Heartbeats

Configure `butler.health.heartbeat.url` and `butler.health.heartbeat.token` to enable.

```php
heartbeat('foo bar'); // POST http://heartbeat.localhost/foo-bar/1

heartbeat('foo baz', 5); // POST http://heartbeat.localhost/foo-baz/5
```

### Log driver

To prevent HTTP requests in local environment, set `butler.health.heartbeat.driver` to "log".

### Fake

Instead of faking the laravel [Http client](https://laravel.com/docs/master/http-client) in your tests you can fake heartbeats, see example below.

```php
public function test_something()
{
    Heartbeat::fake();

    // Assert that nothing was sent...
    Heartbeat::nothingSent();

    // Assert a heartbeat was not sent...
    Heartbeat::assertNotSent('foobar');

    heartbeat('foobar');

    // Assert 1 heartbeat was sent...
    Heartbeat::assertSentCount(1);

    // Assert a heartbeat was sent...
    Heartbeat::assertSent('foobar');
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
