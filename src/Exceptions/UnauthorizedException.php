<?php

namespace OidcAuth\Exceptions;

use Throwable;

class UnauthorizedException extends \Exception
{
    public function __construct(string $message = 'unauthorized', int $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
