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
composer require kalel1500/kalion
```

## Publish files

To publish all the files in the package you can use the following command:

```bash
php artisan vendor:publish --provider="Thehouseofel\Kalion\Infrastructure\KalionServiceProvider"
```

Or else you have the following to publish the files independently

```bash
php artisan vendor:publish --tag="kalion-migrations"
php artisan vendor:publish --tag="kalion-views"
php artisan vendor:publish --tag="kalion-view-layout"
php artisan vendor:publish --tag="kalion-config"
php artisan vendor:publish --tag="kalion-config-auth"
php artisan vendor:publish --tag="kalion-config-links"
php artisan vendor:publish --tag="kalion-lang"
```

## License

Laravel Hexagonal and DDD is open-sourced software licensed under the [GNU General Public License v3.0](LICENSE).
