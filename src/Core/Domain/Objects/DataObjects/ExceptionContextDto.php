<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Domain\Objects\DataObjects;

use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Thehouseofel\Kalion\Core\Domain\Exceptions\Base\KalionHttpException;
use Thehouseofel\Kalion\Core\Domain\Exceptions\Contracts\KalionExceptionInterface;
use Thehouseofel\Kalion\Core\Domain\Objects\DataObjects\Attributes\DisableReflection;
use Throwable;

/**
 * @internal This class is intended for internal package usage only.
 */
#[DisableReflection]
class ExceptionContextDto extends AbstractDataTransferObject
{
    protected readonly array   $texts;
    public readonly int        $statusCode;
    public readonly string     $title;
    public readonly string     $message;
    public readonly bool       $success;
    public readonly ?array     $data;
    public readonly int|string $code;
    public readonly string     $exception;
    public readonly string     $file;
    public readonly int        $line;
    public readonly array      $trace;
    public readonly ?Throwable $previous;
    public readonly bool       $showLogout;
    public readonly array      $debugData;

    public function __construct(
        Throwable $e,
        ?array    $data = null,
        bool      $success = false,
        array     $debugData = [],
    )
    {
        $this->texts = [
            401 => ['title' => kalion()->trans('Unauthorized'),          'message' => kalion()->trans('Unauthorized')                     ],
            402 => ['title' => kalion()->trans('Payment Required'),      'message' => kalion()->trans('Payment Required')                 ],
            403 => ['title' => kalion()->trans('Forbidden'),             'message' => kalion()->trans($e->getMessage() ?: 'Forbidden')    ],
            404 => ['title' => kalion()->trans('Not Found'),             'message' => kalion()->trans('Not Found')                        ],
            419 => ['title' => kalion()->trans('Page Expired'),          'message' => kalion()->trans('Page Expired')                     ],
            429 => ['title' => kalion()->trans('Too Many Requests'),     'message' => kalion()->trans('Too Many Requests')                ],
            500 => ['title' => kalion()->trans('Server Error'),          'message' => kalion()->trans('Server Error')                     ],
            503 => ['title' => kalion()->trans('Service Unavailable'),   'message' => kalion()->trans('Service Unavailable')              ],
        ];

        $this->statusCode     = (method_exists($e, 'getStatusCode')) ? $e->getStatusCode() : 500;
        $this->title          = $this->getTitle();
        $this->message        = $this->getMessage($e);
        $this->success        = $success;
        $this->data           = $data;
        $this->code           = $e->getCode();
        $this->exception      = get_class($e);
        $this->file           = $e->getFile();
        $this->line           = $e->getLine();
        $this->trace          = collect($e->getTrace())->map(fn($trace) => Arr::except($trace, ['args']))->all();
        $this->previous       = $e->getPrevious();
        $this->showLogout     = $e instanceof KalionHttpException && kalion()->config()->exceptionsShowLogoutForm() && $e::SHOW_LOGOUT_FORM;
        $this->debugData      = $debugData;
    }

    public function getTitle(): string
    {
        return $this->texts[$this->statusCode]['title'] ?? kalion()->trans(Response::$statusTexts[$this->statusCode]);
    }

    public function getMessage(Throwable $e): string
    {
        $message = $this->texts[$this->statusCode]['message'] ?? kalion()->trans('Unknown Error');
        return (is_kalion_exception($e) || ($e instanceof HttpException) || kalion()->isDebug()) ? $e->getMessage() : $message;
    }

    /*----------------------------------------------------------------------------------------------------------------*/
    /*---------------------------------------------- Create Functions -----------------------------------------------*/

    public static function from(
        Throwable $e,
        ?array    $data = null,
        bool      $success = false,
        array     $debugData = [],
    ): static
    {
        if ($e instanceof KalionExceptionInterface && $e->getExceptionContext() !== null) {
            return $e->getExceptionContext();
        }

        return new static(
            e             : $e,
            data          : $data,
            success       : $success,
            debugData     : $debugData,
        );
    }

    /*----------------------------------------------------------------------------------------------------------------*/
    /*---------------------------------------------- toArray Functions -----------------------------------------------*/

    private function toArrayForProd(): array
    {
        return [
            'success' => $this->success,
            'message' => $this->message,
            'data'    => $this->data,
        ];
    }

    private function arrayDebugInfo(): array
    {
        $previousData = is_null($this->previous) ? null : static::from($this->previous);
        return [
            'exception'  => $this->exception,
            'file'       => $this->file,
            'line'       => $this->line,
            'trace'      => $this->trace,
            'previous'   => $previousData?->toArray(),
            'debug_data' => $this->debugData,
        ];
    }

    public function toArray(bool $throwInDebugMode = true): array
    {
        $addDebugInfo = kalion()->isDebug() && $throwInDebugMode;
        $toArray      = $this->toArrayForProd();
        return $addDebugInfo ? array_merge($toArray, $this->arrayDebugInfo()) : $toArray;
    }
}
