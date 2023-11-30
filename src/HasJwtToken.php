<?php

namespace OidcAuth;

use OidcAuth\Exceptions\UnauthorizedException;
use OidcAuth\Facade\Oidc;

trait HasJwtToken
{
    protected $token;

    protected $shortToken;

    /**
     * @throws UnauthorizedException
     */
    public function token(): string
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
            $this->shortToken = Oidc::shortUser($this->token(), $this->getKey());
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
