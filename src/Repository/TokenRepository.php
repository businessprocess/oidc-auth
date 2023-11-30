<?php

namespace OidcAuth\Repository;

use OidcAuth\Contracts\Storage;
use OidcAuth\Models\OidcUser;

class TokenRepository
{
    public function __construct(protected Storage $storage)
    {
    }

    public function setUser(OidcUser $user): OidcUser
    {
        $this->setJwt($user->jwt(), $user->getKey());
        $this->setRt($user->rt(), $user->jwt());

        return $user;
    }

    public function fill($data): static
    {
        $jwt = $data['jwt'] ?? null;
        $rt = $data['rt'] ?? null;
        $payload = $data['payload'] ?? null;

        if ($jwt && $payload) {
            $payload = new Payload($payload);

            $this->setJwt($jwt, $payload->getKey());
        }

        if ($jwt && $rt) {
            $this->setRt($rt, $jwt);
        }

        return $this;
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
