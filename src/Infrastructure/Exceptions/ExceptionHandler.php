<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Infrastructure\Exceptions;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Thehouseofel\Kalion\Domain\Contracts\KalionException;
use Thehouseofel\Kalion\Domain\Exceptions\AbortException;
use Thehouseofel\Kalion\Domain\Exceptions\Base\KalionHttpException;
use Thehouseofel\Kalion\Domain\Objects\DataObjects\ExceptionContextDo;
use Throwable;

final class ExceptionHandler
{
    /**
     * Internamente Laravel primero llama al render()
     *
     * Después llama al respond() y este modifica la respuesta
     */
    public static function getUsingCallback(): callable
    {
        return function (Exceptions $exceptions) {

            // Renderizar manualmente los ModelNotFoundException para que todos los "findOrFail()" en local muestren la vista "trace" y en PRO muestren nuestra vita "custom-error" sin tener que envolverlos en un "tryCatch"
            $exceptions->render(function (NotFoundHttpException $e, Request $request) {
                $exception = $e->getPrevious();

                // Si no es una instancia de ModelNotFoundException, devolver null
                if (!($exception instanceof ModelNotFoundException) || self::shouldRenderJson($request)) return null;

                if (debug_is_active()) {
                    return response(get_html_laravel_debug_stack_trace($request, $exception));
                } else {
                    $context = ExceptionContextDo::from($exception);
                    return response()->view('kal::pages.exceptions.error', compact('context'), $context->statusCode);
                }
            });

            // Renderizar nuestras excepciones de dominio
            $exceptions->render(function (KalionException $e, Request $request) {
                $context = $e->getContext();

                // Si se espera un Json, pasarle todos los datos de nuestra "KalionException" [success, message, data]
                if (self::shouldRenderJson($request)) {
                    return response()->json($context->toArray(), $context->statusCode);
                }

                // Si espera una Vista y comprobamos si el debug es true
                if (debug_is_active()) {
                    // Si la excepción es una instancia de "AbortException" renderizamos la vista de errores de Laravel
                    if ($e instanceof AbortException) return response(get_html_laravel_debug_stack_trace($request, $e));

                    // Para cualquier "KalionException" que no sea "KalionHttpException", dejamos que laravel se encargue de renderizar el error.
                    if (!($e instanceof KalionHttpException)) return null;
                }

                // En PROD (o las "KalionHttpException" en DEBUG) devolvemos nuestra vista personalizada
                return response()->view('kal::pages.exceptions.error', compact('context'), $context->statusCode);
            });

            // Indicar a Laravel cuando devolver un Json (mirar url "/ajax/")
            $exceptions->shouldRenderJsonWhen(function (Request $request, Throwable $e) {
                return self::shouldRenderJson($request);
            });

            // Formatear todas las respuestas Json para añadir los parámetros [success, message, data] con un valor por defecto (No aplica en los "KalionException" porque ya tienen ese formato)
            $exceptions->respond(function (Response $response, Throwable $e, Request $request) {
                if ($response instanceof JsonResponse) {
                    $data = json_decode($response->getContent(), true);
                    $data = array_merge(['success' => false, 'message' => '', 'data' => null], $data);
                    return response()->json($data, $response->getStatusCode());
                }
                return $response;
            });

        };
    }

    private static function shouldRenderJson(Request $request): bool
    {
        return $request->expectsJson() || url_contains_ajax();
    }
}
