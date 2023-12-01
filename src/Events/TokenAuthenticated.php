<?php

namespace OidcAuth\Events;

use OidcAuth\Repository\Payload;

class TokenAuthenticated
{
    public function __construct(public string $token, public Payload $payload)
    {
    }
}
