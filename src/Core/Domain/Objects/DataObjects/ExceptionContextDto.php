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
    public readonly int        $statusCode;
    public readonly string     $title;
    public readonly string     $message;
    public readonly bool       $success;
    public readonly ?array     $data;
    public readonly ?array     $custom_response;
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
        ?array    $custom_response = null,
    )
    {
        $this->statusCode      = (method_exists($e, 'getStatusCode')) ? $e->getStatusCode() : 500;
        $this->title           = Response::$statusTexts[$this->statusCode];
        $this->message         = (is_kalion_exception($e) || debug_is_active()) ? $e->getMessage() : __('Server Error');
        $this->success         = $success;
        $this->data            = $data;
        $this->custom_response = $custom_response;
        $this->code            = $e->getCode();
        $this->exception       = get_class($e);
        $this->file            = $e->getFile();
        $this->line            = $e->getLine();
        $this->trace           = collect($e->getTrace())->map(fn($trace) => Arr::except($trace, ['args']))->all();
        $this->previous        = $e->getPrevious();
        $this->showLogout      = $e instanceof KalionHttpException && config('kalion.exceptions.http.show_logout_form') && $e::SHOW_LOGOUT_FORM;
    }

    /*----------------------------------------------------------------------------------------------------------------*/
    /*---------------------------------------------- Create Functions -----------------------------------------------*/

    public static function from(Throwable $e, ?array $data = null, bool $success = false, ?array $custom_response = null): ExceptionContextDto
    {
        if (method_exists($e, 'getContext') && ! is_null($e->getContext())) return $e->getContext();

        return new ExceptionContextDto(
            e              : $e,
            data           : $data,
            success        : $success,
            custom_response: $custom_response,
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
        $previousData = is_null($this->previous) ? null : ExceptionContextDto::from($this->previous);
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
        $toArray      = $this->custom_response ?? $this->toArrayForProd();
        return $addDebugInfo ? array_merge($toArray, $this->arrayDebugInfo()) : $toArray;
    }
}
