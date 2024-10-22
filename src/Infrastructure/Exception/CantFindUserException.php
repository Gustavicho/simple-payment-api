<?php

namespace App\Infrastructure\Exception;

use App\Trait\DebugDetails;
use Symfony\Component\HttpFoundation\Response;

class CantFindUserException extends \JsonException
{
    use DebugDetails;

    public function __construct(
        string $message = 'Can\'t find the user',
        int $statusCode = Response::HTTP_NOT_FOUND,
        ?\Throwable $previous = null,
    ) {
        parent::__construct($message, $statusCode, $previous);
    }
}
