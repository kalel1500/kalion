<?php

use Orchestra\Testbench\Foundation\Application;

require __DIR__ . '/../vendor/autoload.php';

/*
|--------------------------------------------------------------------------
| Crear la aplicación con Orchestra
|--------------------------------------------------------------------------
|
| Esto inicializa una instancia de Laravel igual que cuando corres los tests,
| pero aquí la vamos a usar para responder a peticiones HTTP normales.
|
*/
$app = Application::create();

$app->register(\Thehouseofel\Kalion\KalionServiceProvider::class);

// Configuración BD
$app['config']->set('database.default', 'testing');
$app['config']->set('database.connections.testing', [
    'driver'   => 'sqlite',
    'database' => __DIR__.'/Support/database/database.sqlite',
    'prefix'   => '',
]);

/*
|--------------------------------------------------------------------------
| Registrar Rutas de Debug
|--------------------------------------------------------------------------
|
| Puedes registrar rutas igual que en Laravel. Estas estarán disponibles
| cuando levantes el servidor con "php -S".
|
*/
$app->make('router')->get('/dd', function () {

    try {
        $useCase = new \Thehouseofel\Kalion\Tests\Support\Contexts\Blog\Application\GetPostDataUseCase();
        $posts = $useCase->getPostsWithRelations();
        dd($posts->toArray());
    } catch (\Throwable $th) {
        dd($th);
    }

});

$app->make('router')->get('/', function () {
    return 'Servidor de pruebas para el paquete Kalion. Ir a <a href="/dd">/dd</a>';
});

/*
|--------------------------------------------------------------------------
| Manejar la petición
|--------------------------------------------------------------------------
|
| Esto es básicamente lo mismo que el index.php de Laravel normal.
|
*/
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);
$response->send();
$kernel->terminate($request, $response);
