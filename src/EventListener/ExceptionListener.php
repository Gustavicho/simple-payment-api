<?php

namespace App\EventListener;

use App\Infrastructure\Exception\ValidationException;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

#[AsEventListener(event: 'kernel.exception', method: 'onKernelException')]
class ExceptionListener
{
    private const EXCEPTION_RESPONSE_HTTP_CODE_MAP = [
        ValidationException::class => Response::HTTP_BAD_REQUEST,
        UniqueConstraintViolationException::class => Response::HTTP_CONFLICT,
    ];

    public function onKernelException(ExceptionEvent $event): JsonResponse
    {
        $exception = $event->getThrowable();

        return $this->newJsonResponse($exception);
    }

    private function httpCode(\Throwable $exception): int
    {
        $exceptionClass = get_class($exception);

        return $exception->getCode()
            ?? self::EXCEPTION_RESPONSE_HTTP_CODE_MAP[$exceptionClass]
            ?? Response::HTTP_INTERNAL_SERVER_ERROR;
    }

    private function newJsonResponse(\Throwable $exception): JsonResponse
    {
        $responseData = [
            'status' => 'error',
            'message' => $exception->getMessage(),
            'code' => $this->httpCode($exception),
        ];

        if ($exception instanceof ValidationException) {
            $responseData['errors'] = $exception->getValidationErrors();
        }

        return new JsonResponse($responseData, $this->httpCode($exception));
    }
}
