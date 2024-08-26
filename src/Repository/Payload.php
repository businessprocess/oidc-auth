<?php

namespace OidcAuth\Repository;

class Payload
{
    public static string $defaultRealm = self::REALM_USER;

    public const REALM_USER = 'bpt';

    public const REALM_AXIOMA = 'axioma';

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

    public static function setDefaultRealm(string $defaultRealm): void
    {
        if (in_array($defaultRealm, [self::REALM_USER, self::REALM_AXIOMA])) {
            self::$defaultRealm = $defaultRealm;
        }
    }

    public static function getDefaultRealm(): string
    {
        return self::$defaultRealm;
    }

    public function isWebwellnessRealm(): bool
    {
        return $this->getRealm() === self::REALM_USER;
    }

    public function isAxiomaRealm(): bool
    {
        return $this->getRealm() === self::REALM_AXIOMA;
    }

    public function isUserRealm(): bool
    {
        return $this->isWebwellnessRealm() || $this->isAxiomaRealm();
    }

    public function getKey(): ?string
    {
        return $this->isUserRealm() ? $this->getBptUserId() : $this->getService();
    }

    public function isValidRealm(): bool
    {
        if ($this->isUserRealm()) {
            return $this->getRealm() === self::$defaultRealm;
        }

        return true;
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
