<?php

namespace App\DTO;

class ResponseDTO
{
    public function __construct(
        public bool $success,
        public mixed $data = null,
        public array $raw = [],
        public ?string $error = null,
        public ?int $statusCode = null,
        public array $headers = []
    ) {}

    /**
     * Create success response
     */
    public static function success(mixed $data = null, array $raw = [], int $statusCode = 200): self
    {
        return new self(
            success: true,
            data: $data,
            raw: $raw,
            statusCode: $statusCode
        );
    }

    /**
     * Create error response
     */
    public static function error(string $error, array $raw = [], int $statusCode = 400): self
    {
        return new self(
            success: false,
            error: $error,
            raw: $raw,
            statusCode: $statusCode
        );
    }

    /**
     * Create from HTTP response
     */
    public static function fromHttpResponse(\Illuminate\Http\Client\Response $response, mixed $transformedData = null): self
    {
        $isSuccess = $response->successful();
        $data = $transformedData ?? $response->json();

        return new self(
            success: $isSuccess,
            data: $isSuccess ? $data : null,
            raw: $response->json() ?? [],
            error: $isSuccess ? null : ($data['error'] ?? $response->body()),
            statusCode: $response->status(),
            headers: $response->headers()
        );
    }

    /**
     * Get data or throw exception if error
     */
    public function getDataOrFail(): mixed
    {
        if (!$this->success) {
            throw new \RuntimeException($this->error ?? 'Unknown error occurred');
        }

        return $this->data;
    }

    /**
     * Get specific field from data
     */
    public function getData(string $key, mixed $default = null): mixed
    {
        if (!$this->success || !is_array($this->data)) {
            return $default;
        }

        return data_get($this->data, $key, $default);
    }

    /**
     * Check if response has specific error
     */
    public function hasError(string $errorType): bool
    {
        return !$this->success && str_contains(strtolower($this->error ?? ''), strtolower($errorType));
    }

    /**
     * Get retry delay from headers (for rate limiting)
     */
    public function getRetryAfter(): ?int
    {
        $retryAfter = $this->headers['retry-after'][0] ?? $this->headers['Retry-After'][0] ?? null;

        if (!$retryAfter) {
            return null;
        }

        return is_numeric($retryAfter) ? (int) $retryAfter : null;
    }

    /**
     * Convert to array
     */
    public function toArray(): array
    {
        return [
            'success' => $this->success,
            'data' => $this->data,
            'error' => $this->error,
            'status_code' => $this->statusCode,
        ];
    }
}
