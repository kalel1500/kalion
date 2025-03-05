<?php

declare(strict_types=1);

namespace Thehouseofel\Hexagonal\Domain\Services;

final class TailwindClassFilter {
    protected array $specials = [
        'position' => [
            'static',
            'fixed',
            'absolute',
            'relative',
            'sticky',
        ],
        'visibility' => [
            'visible',
            'invisible',
            'collapse',
        ],
        'display' => [
            'inline',
            'block',
            'inline-block',
            'flow-root',
            'flex',
            'inline-flex',
            'grid',
            'inline-grid',
            'contents',
            'table',
            'inline-table',
            'table-caption',
            'table-cell',
            'table-column',
            'table-column-group',
            'table-footer-group',
            'table-header-group',
            'table-row-group',
            'table-row',
            'list-item',
            'hidden',
            'sr-only',
            'not-sr-only',
        ],
        'text_decoration' => [
            'underline',
            'overline',
            'line-through',
            'no-underline',
        ],
        'text_transform' => [
            'uppercase',
            'lowercase',
            'capitalize',
            'normal-case',
        ],
        'text_overflow' => [
            'truncate',
            'text-ellipsis',
            'text-clip',
        ]
    ];

    // Aquí se definen los grupos especiales. Por ejemplo, para clases de fondo ("bg-")
    // se indica que se deben contar como 3 partes, haciendo que "bg-white" y "bg-red-500"
    // pertenezcan al mismo grupo.
    protected array $groups = [
        'bg-inherit'       => 3,
        'bg-current'       => 3,
        'bg-transparent'   => 3,
        'bg-black'         => 3,
        'bg-white'         => 3,
        'text-inherit'     => 3,
        'text-current'     => 3,
        'text-transparent' => 3,
        'text-black'       => 3,
        'text-white'       => 3,
    ];

    public function __construct(array $specials = null, array $groups = null) {
        if ($specials !== null) {
            $this->specials = $specials;
        }
        if ($groups !== null) {
            $this->groups = $groups;
        }
    }

    /**
     * Filtra las clases por defecto eliminando aquellas que tengan equivalentes
     * en las clases personalizadas.
     *
     * @param string $default_classes
     * @param string $custom_classes
     * @return string
     */
    public function filter(string $default_classes, string $custom_classes): string
    {
        // Convertimos las cadenas en arrays
        $default_array = explode(' ', trim($default_classes));
        $custom_array  = explode(' ', trim($custom_classes));

        $filtered = [];
        foreach ($default_array as $default_class) {
            if (empty($default_class)) continue;
            if ($this->shouldKeepClass($default_class, $custom_array)) {
                $filtered[] = $default_class;
            }
        }

        return implode(' ', $filtered);
    }

    /**
     * Determina si se debe conservar la clase por defecto.
     *
     * La lógica es:
     * - Se filtran las clases custom para considerar solo las que tengan (o no) variante según la clase default.
     * - Si la clase es especial (display, position, etc.), se elimina si en custom existe cualquier clase
     *   del mismo grupo especial.
     * - Si no es especial, se elimina si hay en custom alguna clase que tenga el mismo número de partes (group)
     *   y el mismo prefijo.
     *
     * @param string $default_class
     * @param array $custom_array
     * @return bool
     */
    protected function shouldKeepClass(string $default_class, array $custom_array): bool
    {
        $defaultSpecial = $this->getSpecialGroup($default_class);
        $parsed = $this->parseClass($default_class);

        // Filtrar las clases custom relevantes según si la default tiene variante o no.
        $filteredCustom = array_filter($custom_array, function($custom_class) use ($parsed) {
            $customHasVariant = strpos($custom_class, ':') !== false;
            if ($parsed['variant'] !== '') {
                // Si la clase default tiene variante, consideramos solo las custom que tengan la misma variante.
                return $customHasVariant && (strpos($custom_class, $parsed['variant'] . ':') === 0);
            }
            // Si no tiene variante, tomamos solo las custom sin variante.
            return !$customHasVariant;
        });

        foreach ($filteredCustom as $custom_class) {
            $customParsed = $this->parseClass($custom_class);
            if ($defaultSpecial !== false) {
                // Para clases especiales, se elimina si en custom aparece alguna clase del mismo grupo.
                if (in_array($customParsed['base'], $this->specials[$defaultSpecial])) {
                    return false;
                }
            } else {
                // Para el resto, se elimina si se coincide en el número de partes y el prefijo.
                if ($parsed['group'] === $customParsed['group'] && $parsed['prefix'] === $customParsed['prefix']) {
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * Verifica si la clase pertenece a un grupo especial (por ejemplo, display o position).
     * Devuelve el nombre del grupo o false si no pertenece a ninguno.
     *
     * @param string $class
     * @return mixed
     */
    protected function getSpecialGroup(string $class) {
        $parsed = $this->parseClass($class);
        foreach ($this->specials as $groupKey => $classList) {
            if (in_array($parsed['base'], $classList)) {
                return $groupKey;
            }
        }
        return false;
    }

    /**
     * Descompone una clase de Tailwind en:
     * - variant: parte antes de ":" (vacía si no existe)
     * - base: parte después de ":" o la clase completa si no hay variante
     * - group: cantidad de partes al dividir la base por "-" (se sobrescribe si la clase coincide con un grupo en $groups)
     * - prefix: la primera parte de la base
     *
     * @param string $class
     * @return array
     */
    protected function parseClass(string $class): array
    {
        if (strpos($class, ':') !== false) {
            // Separamos en dos partes: variante y el resto.
            list($variant, $base) = explode(':', $class, 2);
        } else {
            $variant = '';
            $base = $class;
        }

        // Obtener el prefijo (lo que está antes del primer guión).
        $parts = explode('-', $base);
        $prefix = $parts[0];

        // Si la clase coincide con algún grupo definido en $groups (según el prefijo),
        // se asigna ese valor. De lo contrario, se cuenta el número de partes.
        $group = null;
        foreach ($this->groups as $groupKey => $groupCount) {
            // Se obtiene el prefijo de la clave del grupo.
            $groupKeyPrefix = explode('-', $groupKey)[0];
            if ($prefix === $groupKeyPrefix) {
                $group = $groupCount;
                break;
            }
        }
        if ($group === null) {
            $group = count($parts);
        }

        return [
            'variant' => $variant,
            'base'    => $base,
            'group'   => $group,
            'prefix'  => $prefix,
        ];
    }
}
