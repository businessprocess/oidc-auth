<?php

namespace OidcAuth\Repository;

class Credential
{
    private ?string $login;

    private ?string $password;

    public function __construct($credential = [])
    {
        $this->login = $credential['login'] ?? null;
        $this->password = $credential['password'] ?? null;
    }

    public function login(): ?string
    {
        return $this->login;
    }

    public function password(): ?string
    {
        return $this->password;
    }
}
