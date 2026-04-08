<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Domain\Objects\DataObjects;

use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Thehouseofel\Kalion\Core\Domain\Exceptions\Base\KalionHttpException;
use Thehouseofel\Kalion\Core\Domain\Objects\DataObjects\Attributes\DisableReflection;
use Throwable;

/**
 * @internal This class is not meant to be used or overwritten outside the package.
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
    public readonly ?array     $customResponse;
    public readonly int|string $code;
    public readonly string     $exception;
    public readonly string     $file;
    public readonly int        $line;
    public readonly array      $trace;
    public readonly ?Throwable $previous;
    public readonly bool       $showLogout;

    public function __construct(
        Throwable $e,
        ?array    $data = null,
        bool      $success = false,
        ?array    $customResponse = null,
    )
    {
        $this->texts = [
            401 => ['title' => __('Unauthorized'),          'message' => __('Unauthorized')                     ],
            402 => ['title' => __('Payment Required'),      'message' => __('Payment Required')                 ],
            403 => ['title' => __('Forbidden'),             'message' => __($e->getMessage() ?: 'Forbidden')    ],
            404 => ['title' => __('Not Found'),             'message' => __('Not Found')                        ],
            419 => ['title' => __('Page Expired'),          'message' => __('Page Expired')                     ],
            429 => ['title' => __('Too Many Requests'),     'message' => __('Too Many Requests')                ],
            500 => ['title' => __('Server Error'),          'message' => __('Server Error')                     ],
            503 => ['title' => __('Service Unavailable'),   'message' => __('Service Unavailable')              ],
        ];

        $this->statusCode     = (method_exists($e, 'getStatusCode')) ? $e->getStatusCode() : 500;
        $this->title          = $this->getTitle();
        $this->message        = $this->getMessage($e);
        $this->success        = $success;
        $this->data           = $data;
        $this->customResponse = $customResponse;
        $this->code           = $e->getCode();
        $this->exception      = get_class($e);
        $this->file           = $e->getFile();
        $this->line           = $e->getLine();
        $this->trace          = collect($e->getTrace())->map(fn($trace) => Arr::except($trace, ['args']))->all();
        $this->previous       = $e->getPrevious();
        $this->showLogout     = $e instanceof KalionHttpException && config('kalion.exceptions.http.show_logout_form') && $e::SHOW_LOGOUT_FORM;
    }

    public function getTitle(): string
    {
        return $this->texts[$this->statusCode]['title'] ?? __(Response::$statusTexts[$this->statusCode]);
    }

    public function getMessage(Throwable $e): string
    {
        $message = $this->texts[$this->statusCode]['message'] ?? __('Unknown Error');
        return (is_kalion_exception($e) || debug_is_active()) ? $e->getMessage() : $message;
    }

    /*----------------------------------------------------------------------------------------------------------------*/
    /*---------------------------------------------- Create Functions -----------------------------------------------*/

    public static function from(Throwable $e, ?array $data = null, bool $success = false, ?array $customResponse = null): static
    {
        if (method_exists($e, 'getContext') && ! is_null($e->getContext())) return $e->getContext();

        return new static(
            e             : $e,
            data          : $data,
            success       : $success,
            customResponse: $customResponse,
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
            'exception' => $this->exception,
            'file'      => $this->file,
            'line'      => $this->line,
            'trace'     => $this->trace,
            'previous'  => $previousData?->toArray(),
        ];
    }

    public function toArray(bool $throwInDebugMode = true): array
    {
        $addDebugInfo = debug_is_active() && $throwInDebugMode;
        $toArray      = $this->customResponse ?? $this->toArrayForProd();
        return $addDebugInfo ? array_merge($toArray, $this->arrayDebugInfo()) : $toArray;
    }
}
