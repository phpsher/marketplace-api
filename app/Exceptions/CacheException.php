<?php

declare(strict_types=1);

namespace App\Exceptions;

use App\Jobs\SendLogJob;
use App\Services\TelegramLoggerService;
use App\Traits\ResponseTrait;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class CacheException extends Exception
{
    use ResponseTrait;

    public function report(): void
    {
        $exceptionMessage = \sprintf(
            "Exception: %s\nFile: %s:%s\nMessage: %s",
            static::class,
            $this->getFile(),
            $this->getLine(),
            $this->getMessage()
        );

        Log::error('CacheException', (array) $exceptionMessage);
        SendLogJob::dispatch($exceptionMessage, TelegramLoggerService::class);
    }

    public function render(): JsonResponse
    {
        return $this->error(
            message: $this->getMessage(),
        );
    }
}
