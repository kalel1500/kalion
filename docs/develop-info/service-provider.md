## registerMiddlewares

```php

// Registrar/sobreescribir un grupo de middlewares
// $router->middlewareGroup('newCustomGroup', [\Vendor\Package\Http\Middleware\KalionAnyMiddleware::class]);

// Añadir middlewares al final de un grupo
// $router->pushMiddlewareToGroup('web', ShareInertiaData::class); // $kernel = $this->app->make(Kernel::class); $kernel->appendMiddlewareToGroup('web', ShareInertiaData::class);

// Añadir middlewares al principio de un grupo
// $router->prependMiddlewareToGroup('web', ShareInertiaData::class);

```
