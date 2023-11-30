<?php

namespace OidcAuth;

use OidcAuth\Exceptions\UnauthorizedException;
use OidcAuth\Facade\Oidc;

trait HasJwtToken
{
    protected $token;

    protected $shortToken;

    /**
     * @return string|null
     */
    public function token()
    {
        if ($this->token && Oidc::check($this->token)) {
            return $this->token;
        }

        return $this->token = Oidc::token($this->getKey());
    }

    /**
     * @return string
     *
     * @throws UnauthorizedException
     */
    public function shortToken()
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
