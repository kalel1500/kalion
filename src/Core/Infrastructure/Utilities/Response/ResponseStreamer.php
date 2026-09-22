<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Infrastructure\Utilities\Response;

use Illuminate\Support\Traits\Macroable;

class ResponseStreamer
{
    use Macroable;

    public function echo(string $content): static
    {
        echo $content;
        $this->sendBuffer();

        return $this;
    }

    private function sendBuffer(): void
    {
        if (ob_get_level() > 0) {
            ob_flush();
        }

        flush();
    }
}
