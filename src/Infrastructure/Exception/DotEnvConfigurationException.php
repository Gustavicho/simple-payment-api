<?php

namespace App\Infrastructure\Exception;

use App\Trait\DebugDetails;
use Symfony\Component\HttpFoundation\Response;

class DotEnvConfigurationException extends \JsonException
{
    use DebugDetails;

    public function __construct(
        string $message = 'The app has internal problems',
        int $statusCode = Response::HTTP_INTERNAL_SERVER_ERROR,
        ?\Throwable $previous = null,
    ) {
        parent::__construct($message, $statusCode, $previous);
    }
}
