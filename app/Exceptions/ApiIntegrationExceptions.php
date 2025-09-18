<?php

namespace App\Exceptions;

use Exception;

class MarketplaceAuthException extends Exception
{
    public function __construct(string $message = 'Marketplace authentication failed', int $code = 401, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}

class MarketplaceRateLimitException extends Exception
{
    public function __construct(
        string $message = 'Rate limit exceeded',
        public readonly ?int $retryAfter = null,
        int $code = 429,
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}

class MarketplaceValidationException extends Exception
{
    public function __construct(
        string $message = 'Validation failed',
        public readonly array $errors = [],
        int $code = 422,
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}

class Ga4Exception extends Exception
{
    public function __construct(string $message = 'GA4 API error', int $code = 500, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
