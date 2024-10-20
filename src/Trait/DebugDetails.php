<?php

namespace App\Trait;

trait DebugDetails
{
    /**
     * Method for debugging.
     */
    public function getDebugDetails(): array
    {
        return [
            'message' => $this->getMessage(),
            'statusCode' => $this->getCode(),
            'file' => $this->getFile(),
            'line' => $this->getLine(),
            'trace' => $this->getTraceAsString(),
            'previous' => $this->getPrevious() ? $this->getPrevious()->getMessage() : null,
        ];
    }
}
