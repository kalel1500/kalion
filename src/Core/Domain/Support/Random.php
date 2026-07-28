<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Domain\Support;

use Illuminate\Support\Traits\Macroable;
use Thehouseofel\Kalion\Core\Domain\Exceptions\Base\KalionRuntimeException;
use Thehouseofel\Kalion\Core\Domain\Exceptions\InvalidValueException;

class Random
{
    use Macroable;

    public static function weighted(
        int $quantity,
        int $minValue,
        int $maxValue,
        array $customWeights = []
    ): array
    {
        // Validar parámetros
        if ($minValue > $maxValue) {
            throw new InvalidValueException(__('k::error.min_value_cant_be_greater_than_max'));
        }

        if ($quantity <= 0) {
            throw new InvalidValueException(__('k::error.amount_must_be_greater_than_number', ['number' => '0']));
        }

        // Lista de todos los valores posibles
        $range = range($minValue, $maxValue);

        // Comprobar que los pesos personalizados no superan el 100%
        $totalCustomWeight = array_sum($customWeights);
        if ($totalCustomWeight > 100) {
            throw new InvalidValueException(__('k::error.sum_of_probabilities_cant_be_greater_than_100'));
        }

        // Buscar qué números aún no tienen un peso asignado
        $remainingNumbers = array_diff($range, array_keys($customWeights));
        $remainingCount   = count($remainingNumbers);

        // Porcentaje que queda libre para repartir
        $remainingWeight = 100 - $totalCustomWeight;

        // Copiamos los pesos recibidos
        $weights = $customWeights;

        // Repartimos el porcentaje restante de forma uniforme
        // entre los números que no tenían peso definido.
        if ($remainingCount > 0) {
            $weightPerNumber = $remainingWeight > 0
                ? $remainingWeight / $remainingCount
                : 0;

            foreach ($remainingNumbers as $number) {
                $weights[$number] = $weightPerNumber;
            }
        }

        // Eliminar números con probabilidad 0, ya que nunca podrán salir
        $weights = array_filter(
            $weights,
            fn (float $weight): bool => $weight > 0
        );

        if (empty($weights)) {
            throw new KalionRuntimeException(__('k::error.generated_distribution_empty'));
        }

        // Crear una distribución acumulada O(n) en memoria, sin perder precisión decimal
        //
        // Ejemplo:
        // Peso original:
        //   1 => 10
        //   2 => 50
        //   3 => 40
        //
        // Distribución acumulada:
        //   [1, 10]
        //   [2, 60]
        //   [3, 100]
        //
        // Esto define los intervalos:
        //   0..10   -> 1
        //   10..60  -> 2
        //   60..100 -> 3
        $cumulative = [];
        $total = 0.0;

        foreach ($weights as $number => $weight) {
            $total += $weight;
            $cumulative[] = [$number, $total];
        }

        $results = [];

        // Generar tantos números aleatorios como se hayan solicitado
        for ($i = 0; $i < $quantity; $i++) {

            // Número aleatorio entre 0 y el peso total.
            // Será el punto que caerá dentro de uno de los intervalos.
            $target = random_int(0, PHP_INT_MAX - 1) / PHP_INT_MAX * $total;

            // Buscar el primer intervalo que contenga ese punto.
            foreach ($cumulative as [$number, $upperBound]) {
                if ($target <= $upperBound) {
                    $results[] = $number;
                    break;
                }
            }
        }

        return $results;
    }
}
