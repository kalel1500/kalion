<p align="center"><img src="./art/title3.png" alt="Laravel Hexagonal and DDD"></p>

<p align="center">
    <!-- <a href="https://github.com/kalel1500/kalion/actions/workflows/tests.yml"><img src="https://github.com/kalel1500/kalion/actions/workflows/tests.yml/badge.svg" alt="Build Status"></a> -->
    <a href="https://packagist.org/packages/kalel1500/kalion" target="_blank"><img src="https://img.shields.io/packagist/dt/kalel1500/kalion" alt="Total Downloads"></a>
    <a href="https://packagist.org/packages/kalel1500/kalion" target="_blank"><img src="https://img.shields.io/packagist/v/kalel1500/kalion" alt="Latest Stable Version"></a>
    <a href="https://packagist.org/packages/kalel1500/kalion" target="_blank"><img src="https://img.shields.io/packagist/l/kalel1500/kalion" alt="License"></a>
</p>

## ✨ Features

- Utilities for developing in hexagonal architecture and DDD in Laravel.

## Quick start

```bash
composer require kalel1500/kalion:@beta
```

## Publish files

To publish all the files in the package you can use the following command:

```bash
php artisan vendor:publish --provider="Thehouseofel\Kalion\KalionServiceProvider"
```

Or else you have the following to publish the files independently

```bash
php artisan vendor:publish --tag="kalion-migrations"
php artisan vendor:publish --tag="kalion-views"
php artisan vendor:publish --tag="kalion-view-layout"
php artisan vendor:publish --tag="kalion-config"
php artisan vendor:publish --tag="kalion-config-user"
php artisan vendor:publish --tag="kalion-config-links"
php artisan vendor:publish --tag="kalion-lang"
```

## Start command

After installing the package, to start a project you can run the following command.

```bash
php artisan kalion:start
```

This command will modify several project files to the recommended settings.

In addition, it will generate new files to add complete examples to the project with the following views:
- Home
- Posts
- Tags

## Package configuration

### Redirect after login

You can configure where the application will redirect (as long as no previous route is found) in three ways:

1. Overriding the `kalion.auth.redirect_after_login` configuration in `config/kalion.php` file:

   ```php
   return [
       'auth' => [
           'redirect_after_login' => env('KALION_AUTH_REDIRECT_AFTER_LOGIN', 'home')
       ]
   ];
   ```

2. Using the `KALION_AUTH_REDIRECT_AFTER_LOGIN` environment variable:

   ```dotenv
   KALION_AUTH_REDIRECT_AFTER_LOGIN=home
   ```

3. Or using the `redirectAfterLoginTo()` method of the `Kalion` class in the `register` method of a ServiceProvider for a more complex configuration:

   ```php
   public function register(): void
   {
       \Thehouseofel\Kalion\Core\Infrastructure\Support\Services\Kalion::redirectAfterLoginTo('home');
   }
   ```

   > This method also accepts a callback.

### Default path

You can configure where the application will redirect to by default in three ways:

1. Overriding the `kalion.default_path` configuration 

   ```php
   return [
       'default_path' => env('KALION_DEFAULT_PATH', 'home')
   ];
   ```
   
2. Using the `KALION_DEFAULT_PATH` environment variable:

   ```dotenv
   KALION_DEFAULT_PATH=home
   ```

3. Or using the `redirectAfterLoginTo()` method of the `Kalion` class in the `register` method of a ServiceProvider for a more complex configuration:

   ```php
   public function register(): void
   {
       \Thehouseofel\Kalion\Core\Infrastructure\Support\Services\Kalion::redirectDefaultPathTo('home');
   }
   ```

   > This method also accepts a callback.

## License

Kalion is open-sourced software licensed under the [GNU General Public License v3.0](LICENSE).
