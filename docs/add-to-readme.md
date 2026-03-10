
## Package configuration

### Exceptions

```php
use Thehouseofel\Kalion\Core\Infrastructure\Laravel\Exceptions\ExceptionHandler;

->withExceptions(function (Exceptions $exceptions): void {
    ExceptionHandler::handle($exceptions);
})
 ```

### Timezone

```php
    'timezone' => 'Europe/Madrid',
 ```

### Overwriting user types

```php

if (! function_exists('user')) {
    /**
     * Get the currently authenticated user entity.
     *
     * @param string|null $guard
     * @return UserEntity|ApiUserEntity|null
     */
    function user(string $guard = null)
    {
        return \Thehouseofel\Kalion\Core\Infrastructure\Laravel\Facades\Auth::user($guard);
    }
}

 ```
