
## Package configuration

### Exceptions

```php
use Thehouseofel\Kalion\Core\Infrastructure\Exceptions\ExceptionHandler;

->withExceptions(function (Exceptions $exceptions): void {
    ExceptionHandler::handle($exceptions);
})
 ```

### Timezone

```php
    'timezone' => 'Europe/Madrid',
 ```

