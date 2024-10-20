<?php

namespace App\Infrastructure\Exception;

use App\Trait\DebugDetails;
use Symfony\Component\HttpFoundation\Response;

class UserDontHasPermissioException extends \JsonException
{
    use DebugDetails;

    public function __construct(
        string $message = 'You don\'t have permission to do this action',
        int $statusCode = Response::HTTP_FORBIDDEN,
        ?\Throwable $previous = null,
    ) {
        parent::__construct($message, $statusCode, $previous);
    }
}
