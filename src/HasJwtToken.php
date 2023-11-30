<?php

namespace OidcAuth;

use OidcAuth\Facade\Oidc;

trait HasJwtToken
{
    protected $token;

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
     * @return string|null
     */
    public function short()
    {
        $token = $this->token();

        return $token ? Oidc::short($token) : null;
    }

    /**
     * @return string|null
     */
    public function shortUser()
    {
        $token = $this->token();

        return $token ? Oidc::shortUser($token, $this->getKey()) : null;
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
