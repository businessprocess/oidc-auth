<?php

namespace OidcAuth\Repository;

use OidcAuth\Contracts\Storage;
use OidcAuth\Models\OidcUser;
use OidcAuth\Repository\Storage\ArrayStorage;

class TokenRepository
{
    private Storage $storage;

    public function __construct(?Storage $storage = null)
    {
        $this->storage = $storage ?? new ArrayStorage;
    }

    public function update(OidcUser $user): OidcUser
    {
        $this->setJwt($user->token(), $user->getKey());
        $this->setRt($user->rt(), $user->token());

        return $user;
    }

    private function setJwt(string $jwt, $key): void
    {
        $this->storage->set("jwt.$key", $jwt);
    }

    private function setRt(string $jwt, $key): void
    {
        $this->storage->set("rt.$key", $jwt);
    }

    public function jwt($key)
    {
        return $this->storage->get("jwt.$key");
    }

    public function rt($key)
    {
        return $this->storage->get("rt.$key");
    }

    public function publicKey(): ?string
    {
        return $this->storage->get('public-key');
    }

    public function setPublicKey(?string $key): void
    {
        $this->storage->set('public-key', $key);
    }
}
