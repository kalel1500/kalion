<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Infrastructure\Exceptions;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Thehouseofel\Kalion\Core\Domain\Exceptions\Base\KalionHttpException;
use Thehouseofel\Kalion\Core\Domain\Exceptions\Contracts\KalionExceptionInterface;
use Thehouseofel\Kalion\Core\Domain\Objects\DataObjects\ExceptionContextDto;
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

                // Solo intervenimos si la excepción original es un ModelNotFoundException
                if (! ($exception instanceof ModelNotFoundException)) {
                    return null; // Que Laravel lo maneje como siempre
                }

                $context = ExceptionContextDto::from($exception);
                $isJson  = self::shouldRenderJson($request);
                $isDebug = debug_is_active();

                // Si la respuesta esperada es JSON
                if ($isJson) {
                    return $isDebug
                        ? self::renderJson($context)
                        : null; // Deja que Laravel lo maneje con su JSON genérico
                }

                // Si la respuesta es HTML
                return $isDebug
                    ? self::renderHtmlDebug($exception, $request)
                    : self::renderHtmlCustom($context);
            });

            // Renderizar nuestras excepciones de dominio
            $exceptions->render(function (KalionExceptionInterface $e, Request $request) {
                $context  = $e->getContext();
                $notDebug = ! debug_is_active();

                // Si se espera un Json, pasarle todos los datos de nuestra "KalionException" [success, message, data]
                if (self::shouldRenderJson($request)) {
                    return self::renderJson($context);
                }

                /**
                 * Devolver la vista de error personalizada solo si se cumple alguna de estas opciones (or):
                 *  - El debug está desactivado
                 *  - Si la excepcion es una instancia de "KalionHttpException" y la constante "SHOULD_RENDER_TRACE" es "false"
                 */
                if ($notDebug || (self::isKalionHttpExceptionAnd($e, shouldRenderTrace: false))) {
                    return self::renderHtmlCustom($context);
                }

                /**
                 * Para este punto el APP_DEBUG es siempre "true".
                 *
                 * Forzar el "debug_stack_trace" si se cumplen estas opciones (and):
                 *  - El debug está desactivado
                 *  - Si la excepcion es una instancia de "KalionHttpException" y la constante "SHOULD_RENDER_TRACE" es "true"
                 */
                if (self::isKalionHttpExceptionAnd($e, shouldRenderTrace: true)) {
                    return self::renderHtmlDebug($e, $request);
                }

                /**
                 * Para este punto el APP_DEBUG es siempre "true".
                 *
                 * Por lo que dejamos que laravel se encargue de renderizar el error.
                 *
                 * En teoria siempre deberia pintar el "renderHtmlDebug" pero me parece mejor dejar que lo haga laravel en vez de forzarlo manualmente.
                 */
                return null;
            });

            // Indicar a Laravel cuando devolver un Json (mirar url "/ajax/")
            $exceptions->shouldRenderJsonWhen(function (Request $request, Throwable $e) {
                return self::shouldRenderJson($request);
            });

            // Formatear todas las respuestas Json para añadir los parámetros [success, message, data] con un valor por defecto (No aplica en los "KalionException" porque ya tienen ese formato)
            $exceptions->respond(function (SymfonyResponse $response, Throwable $e, Request $request) {
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

    private static function renderJson(ExceptionContextDto $context): JsonResponse
    {
        return response()->json($context->toArray(), $context->statusCode);
    }

    private static function renderHtmlDebug(\Throwable $exception, Request $request): Response
    {
        return response(get_html_laravel_debug_stack_trace($request, $exception));
    }

    private static function renderHtmlCustom(ExceptionContextDto $context): Response
    {
        return response()->view('kal::pages.exceptions.error', compact('context'), $context->statusCode);
    }

    private static function isKalionHttpExceptionAnd(\Throwable $e, bool $shouldRenderTrace): bool
    {
        if (!($e instanceof KalionHttpException)) {
            return false;
        }

        if ($shouldRenderTrace) {
            return $e::SHOULD_RENDER_TRACE;
        } else {
            return ! $e::SHOULD_RENDER_TRACE;
        }
    }
}
