<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Concerns;

use ReflectionClass;

trait CountMethods
{
    protected function countPublicMethods(): int
    {
//        $numeroDeMetodos = count(get_class_methods($this));
//        dd($numeroDeMetodos);

        // Crear una instancia de ReflectionClass para la clase actual
        $reflexion = new ReflectionClass($this); // REFLECTION - once

        // Obtener los métodos públicos o protegidos (sin incluir los estáticos)
        $methods = $reflexion->getMethods(\ReflectionMethod::IS_PUBLIC);

        // Filtrar los métodos que NO sean estáticos
        $filteredMethods = array_filter($methods, fn($method) => !$method->isStatic() && $method->getName() !== '__construct');

        // Contar los métodos filtrados
        return count($filteredMethods);
    }
}
