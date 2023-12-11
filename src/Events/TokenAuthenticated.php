<?php

namespace OidcAuth\Events;

use OidcAuth\Models\OidcUser;

class TokenAuthenticated
{
    public function __construct(public OidcUser $user)
    {
    }
}
