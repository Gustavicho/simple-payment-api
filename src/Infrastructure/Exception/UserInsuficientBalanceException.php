<?php

namespace App\Infrastructure\Exception;

use App\Trait\DebugDetails;
use Symfony\Component\HttpFoundation\Response;

class UserInsuficientBalanceException extends \JsonException
{
    use DebugDetails;

    public function __construct(
        string $message = 'The user has insufficient balance for this action',
        int $statusCode = Response::HTTP_PAYMENT_REQUIRED,
        ?\Throwable $previous = null,
    ) {
        parent::__construct($message, $statusCode, $previous);
    }
}
