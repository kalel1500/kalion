
# Tips para el desarrollo del paquete

## Excluir carpetas al comparar dos proyectos

```regexp
!vendor/*&!node_modules/*&!.idea/*
```

## Instalar la versión "dev-master" del paquete con un enlace durante el desarrollo

```json
{
  "require": {
    "kalel1500/kalion": "dev-master"
  },
  "minimum-stability": "dev",
  "repositories": [
    {
      "type": "path",
      "url": "../kalion"
    }
  ]
}
```

## Configurar variables de entorno durante el desarrollo

Durante el desarrollo, en la aplicación se pueden configurar las siguientes variables para que el comando ""

```dotenv
KALION_PACKAGE_IN_DEVELOP=true
KALION_KEEP_MIGRATIONS_DATE=true
```

## Lanzar los tests

```bash
./vendor/bin/phpunit
```

## Iniciar el servidor de test

```bash
php -S localhost:8000 tests/server.php
```
