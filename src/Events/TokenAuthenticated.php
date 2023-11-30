<?php

namespace OidcAuth\Events;

class TokenAuthenticated
{
    public function __construct(public $token)
    {
    }
}
