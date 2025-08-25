<?php

namespace Thehouseofel\Kalion\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Thehouseofel\Kalion\Infrastructure\KalionServiceProvider;

abstract class TestCase extends Orchestra
{
    /** Flag para ejecutar migraciones + seed solo una vez por ejecución de phpunit */
    protected static bool $migrated = false;

    /** Ruta del fichero sqlite que usaremos durante los tests */
    protected static string $sqliteFile = __DIR__ . '/Support/database/database.sqlite';

    protected function getPackageProviders($app)
    {
        return [
            KalionServiceProvider::class,
        ];
    }

    /**
     * Define config del entorno para la app que Testbench creará.
     * Aquí forzamos la conexión 'testing' a usar el sqlite en disco.
     */
    protected function defineEnvironment($app)
    {
        $app['config']->set('kalion.fresh_database', (bool) env('FRESH_DATABASE', false));
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver'   => 'sqlite',
            'database' => self::$sqliteFile,
            'prefix'   => '',
        ]);
    }

    /**
     * Se ejecuta al inicio de cada test. Aquí usamos
     * un flag estático self::$migrated para ejecutar migrate+seed sólo una vez
     * durante toda la ejecución de phpunit.
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Ejecutar migraciones + seed SOLO LA PRIMERA VEZ
        if (self::$migrated) {
            return;
        }

        // Aseguramos que exista el directorio "database"
        $dbPath = self::$sqliteFile;
        $dir    = dirname($dbPath);
        if (! is_dir($dir)) {
            mkdir($dir, 0775, true);
        }

        $existDatabase = file_exists($dbPath);

        if ($existDatabase && ! config('kalion.fresh_database')) {
            return;
        }

        // Si quieres siempre empezar limpio por ejecución completa de phpunit, descomenta la siguiente línea para eliminar el fichero anterior.
        // if ($existDatabase) { @unlink($dbPath); }
        if (! $existDatabase) {
            touch($dbPath);
        }

        // Si tu paquete tiene migrations públicas, también las registramos
        if (is_dir(__DIR__ . '/../database/migrations')) {
            $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        }

        // Registrar migraciones de tests/Support (se usan por migrate:fresh)
        $this->loadMigrationsFrom(__DIR__ . '/Support/database/migrations');

        // Ejecutar el seeder principal de soporte: ajusta el namespace si lo tienes distinto
        $seedExit = $this->artisan('db:seed', [
            '--class'    => \Thehouseofel\Kalion\Tests\Support\Database\Seeders\DatabaseSeeder::class,
            '--database' => 'testing',
            '--force'    => true,
        ])->run();

        if ($seedExit !== 0) {
            throw new \RuntimeException("db:seed (DatabaseSeeder) falló con código $seedExit");
        }

        // Marcamos como migrado para no volver a ejecutar durante este run
        self::$migrated = true;
    }
}
