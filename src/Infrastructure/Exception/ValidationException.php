<?php

namespace App\Infrastructure\Exception;

use App\Trait\DebugDetails;
use Symfony\Component\HttpFoundation\Response;

class ValidationException extends \JsonException
{
    use DebugDetails;

    private array $validationErrors;

    public function __construct(
        array $validationErrors,
        string $message = 'Validation failed',
        int $statusCode = Response::HTTP_BAD_REQUEST,
        ?\Throwable $previous = null,
    ) {
        $this->validationErrors = $validationErrors;

        parent::__construct($message, $statusCode, $previous);
    }

    /**
     * Return a array where each key is a field name.
     */
    public function getValidationErrors(): array
    {
        $messages = [];
        foreach ($this->validationErrors as $paramName => $violationList) {
            foreach ($violationList as $violation) {
                $messages[$paramName][] = $violation->getMessage();
            }
        }

        return $messages;
    }
}
