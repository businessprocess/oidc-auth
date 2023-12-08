<?php

namespace OidcAuth;

use OidcAuth\Exceptions\UnauthorizedException;
use OidcAuth\Facade\Oidc;

trait HasJwtToken
{
    protected $token;

    protected $shortToken;

    public function token()
    {
        return $this->token;
    }

    /**
     * @throws UnauthorizedException
     */
    public function jwtToken(): string
    {
        if ($this->token && Oidc::check($this->token)) {
            return $this->token;
        }

        return $this->token = Oidc::token($this->getKey());
    }

    /**
     * @throws UnauthorizedException
     */
    public function shortToken(): string
    {
        if (! $this->shortToken) {
            $this->shortToken = Oidc::shortUser($this->jwtToken(), $this->getKey());
        }

        return $this->shortToken;
    }

    /**
     * @return $this
     */
    public function withToken($token)
    {
        $this->token = $token;

        return $this;
    }
}
