<?php

namespace JeffersonGoncalves\Trustpilot\Exceptions;

use RuntimeException;

/**
 * Raised when the Trustpilot API answers a request with a non-2xx HTTP status,
 * when the credentials cannot be exchanged for an access token, or when a call
 * that needs a business unit id has none configured. Carries the response's
 * error message and the HTTP status code (0 for configuration errors).
 */
class TrustpilotException extends RuntimeException
{
    public function __construct(string $message, public readonly int $statusCode = 0)
    {
        parent::__construct($message, $statusCode);
    }
}
