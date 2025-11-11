<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Domain\Objects\DataObjects;

use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Thehouseofel\Kalion\Core\Domain\Objects\DataObjects\Attributes\DisableReflection;
use Throwable;

/**
 * @internal This class is not meant to be used or overwritten outside the package.
 */
#[DisableReflection]
final class ExceptionContextDto extends AbstractDataTransferObject
{
    public readonly string $title;

    public function __construct(
        public readonly int        $statusCode,
        public readonly string     $message,
        public readonly bool       $success,
        public readonly ?array     $data,
        public readonly ?array     $custom_response,
        public readonly int|string $code,
        public readonly string     $exception,
        public readonly string     $file,
        public readonly int        $line,
        public readonly array      $trace,
        public readonly ?Throwable $previous
    )
    {
        $this->title = Response::$statusTexts[$this->statusCode];
    }

    /*----------------------------------------------------------------------------------------------------------------*/
    /*---------------------------------------------- Create Functions -----------------------------------------------*/

    public static function from(Throwable $e, ?array $data = null, bool $success = false, ?array $custom_response = null): ExceptionContextDto
    {
        // if (is_null($e)) return null; // TODO Canals - pensar

        if (method_exists($e, 'getContext') && ! is_null($e->getContext())) return $e->getContext();

        return ExceptionContextDto::fromArray([
            'statusCode'      => (method_exists($e, 'getStatusCode')) ? $e->getStatusCode() : 500,
            'message'         => ExceptionContextDto::getMessage($e),
            'success'         => $success,
            'data'            => $data,
            'custom_response' => $custom_response,
            'code'            => $e->getCode(),
            'exception'       => get_class($e),
            'file'            => $e->getFile(),
            'line'            => $e->getLine(),
            'trace'           => collect($e->getTrace())->map(fn($trace) => Arr::except($trace, ['args']))->all(),
            'previous'        => $e->getPrevious()
        ]);
    }

    public static function getMessage(Throwable $e): string
    {
        return (is_kalion_exception($e) || debug_is_active()) ? $e->getMessage() : __('Server Error');
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
        return [
            'exception' => $this->exception,
            'file'      => $this->file,
            'line'      => $this->line,
            'trace'     => $this->trace,
            'previous'  => $this->getPreviousData()?->toArray(),
        ];
    }

    public function toMakeArray(): array
    {
        return [
            'statusCode' => $this->statusCode,
            'message'    => $this->message,
            'success'    => $this->success,
            'data'       => $this->data,
            'code'       => $this->code,
            'exception'  => $this->exception,
            'file'       => $this->file,
            'line'       => $this->line,
            'trace'      => $this->trace,
            'previous'   => $this->previous,
        ];
    }

    public function toArray(bool $throwInDebugMode = true): array
    {
        $addDebugInfo = debug_is_active() && $throwInDebugMode;
        $toArray      = $this->custom_response ?? $this->toArrayForProd();
        return $addDebugInfo ? array_merge($toArray, $this->arrayDebugInfo()) : $toArray;
    }


    /*----------------------------------------------------------------------------------------------------------------*/
    /*---------------------------------------------------- Properties -------------------------------------------------*/

    public function getPreviousData(): ?ExceptionContextDto
    {
        return is_null($this->previous) ? null : ExceptionContextDto::from($this->previous);
    }

    /*public function getLastPrevious(): ?Throwable
    {
        $exception = $this->previous; // Empieza desde la propiedad $previous

        // Itera sobre las excepciones previas
        while ($exception && $exception->getPrevious()) {
            $exception = $exception->getPrevious();
        }

        // Retorna la excepción más interna o null si no hay más
        return $exception;
    }*/
}
