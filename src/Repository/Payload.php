<?php

namespace OidcAuth\Repository;

class Payload
{
    public static string $defaultRealm = self::REALM_WEBWELLNESS;

    public const REALM_WEBWELLNESS = 'bpt';

    public const REALM_AXIOMA = 'axioma';

    public const REALM_SERVICE = 'service';

    public const REALM_ALIAS = [
        self::REALM_WEBWELLNESS => 'ww',
        self::REALM_AXIOMA => 'binarAxioma',
    ];

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
        if (in_array($defaultRealm, array_merge([self::REALM_WEBWELLNESS, self::REALM_AXIOMA], self::REALM_ALIAS))) {
            self::$defaultRealm = $defaultRealm;
        }
    }

    public static function getDefaultRealm(): string
    {
        return self::$defaultRealm;
    }

    public function isWebwellnessRealm(): bool
    {
        return in_array($this->getRealm(), [self::REALM_WEBWELLNESS, self::REALM_ALIAS[self::REALM_WEBWELLNESS]]);
    }

    public function isAxiomaRealm(): bool
    {
        return in_array($this->getRealm(), [self::REALM_AXIOMA, self::REALM_ALIAS[self::REALM_AXIOMA]]);
    }

    public function isUserRealm(): bool
    {
        return $this->isWebwellnessRealm() || $this->isAxiomaRealm();
    }

    public function getKey(): ?string
    {
        return ! $this->isUserRealm() ? $this->getService() : $this->getBptUserId();
    }

    public function isValidRealm(): bool
    {
        if ($this->isUserRealm()) {
            return in_array(self::$defaultRealm, [$this->getRealm(), self::REALM_ALIAS[$this->getRealm()]]);
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
        return $this->isValidRealm() ? $this->bptUserId : null;
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
