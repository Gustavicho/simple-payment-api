<?php

namespace App\Infrastructure\Exception;

use App\Trait\DebugDetails;
use Symfony\Component\HttpFoundation\Response;

class NotifierNotAvalibleException extends \JsonException
{
    use DebugDetails;

    public function __construct(
        string $message = 'This service is not available. Please try again later',
        int $statusCode = Response::HTTP_GATEWAY_TIMEOUT,
        ?\Throwable $previous = null,
    ) {
        parent::__construct($message, $statusCode, $previous);
    }
}
