<?php

namespace OidcAuth\Http;

use ArrayAccess;
use LogicException;
use OidcAuth\Exceptions\UnauthorizedException;

class Response implements ArrayAccess
{
    /**
     * The underlying PSR response.
     *
     * @var \Psr\Http\Message\ResponseInterface
     */
    protected $response;

    /**
     * The decoded JSON response.
     *
     * @var array
     */
    protected $decoded;

    public function __construct($response)
    {
        $this->response = $response;
    }

    public function status(): int
    {
        return (int) $this->response->getStatusCode();
    }

    public function successful(): bool
    {
        return $this->status() >= 200 && $this->status() < 300;
    }

    public function failed(): bool
    {
        return $this->serverError() || $this->clientError();
    }

    public function clientError(): bool
    {
        return $this->status() >= 400 && $this->status() < 500;
    }

    public function serverError(): bool
    {
        return $this->status() >= 500;
    }

    public function toException()
    {
        if ($this->failed()) {
            return new UnauthorizedException();
        }
    }

    public function throw(): static
    {
        $callback = func_get_args()[0] ?? null;

        if ($this->failed()) {
            throw tap($this->toException(), function ($exception) use ($callback) {
                if ($callback && is_callable($callback)) {
                    $callback($this, $exception);
                }
            });
        }

        return $this;
    }

    public function body(): string
    {
        return (string) $this->response->getBody();
    }

    public function json($key = null, $default = null)
    {
        if (! $this->decoded) {
            $this->decoded = json_decode($this->body(), true);
        }

        if (is_null($key)) {
            return $this->decoded;
        }

        return data_get($this->decoded, $key, $default);
    }

    public function offsetExists($offset): bool
    {
        return isset($this->json()[$offset]);
    }

    public function offsetGet($offset): mixed
    {
        return $this->json()[$offset];
    }

    public function offsetSet($offset, $value): void
    {
        throw new LogicException('Response data may not be mutated using array access.');
    }

    public function offsetUnset($offset): void
    {
        throw new LogicException('Response data may not be mutated using array access.');
    }

    public function __toString()
    {
        return $this->body();
    }
}
