<?php

namespace OidcAuth\Exceptions;

use Throwable;

class UnauthorizedException extends \Exception
{
    public function __construct(string $message = 'Unauthorized', int $code = 401, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
