<?php

namespace OidcAuth\Repository;

class Payload
{
    public const REALM_USER = 'bpt';

    public const REALM_SERVICE = 'service';

    private string $realm;

    private ?string $service = null;

    private ?string $bptUserId = null;

    private int $iat;

    private int $exp;

    private array $attributes;

    public function __construct($data = [])
    {
        $this->realm = $data['realm'];
        $this->service = $data['service'] ?? null;
        $this->bptUserId = $data['bptUserId'] ?? null;
        $this->iat = $data['iat'] ?? 0;
        $this->exp = $data['exp'] ?? 0;

        $this->attributes = $data;
    }

    public function isUserRealm(): bool
    {
        return $this->getRealm() === self::REALM_USER;
    }

    public function getKey(): ?string
    {
        return $this->isUserRealm() ? $this->getBptUserId() : $this->getService();
    }

    public function getRealm(): string
    {
        return $this->realm;
    }

    public function getService(): ?string
    {
        return $this->service;
    }

    public function getBptUserId(): ?string
    {
        return $this->bptUserId;
    }

    public function getIat(): int
    {
        return $this->iat;
    }

    public function getExp(): int
    {
        return $this->exp;
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }
}
