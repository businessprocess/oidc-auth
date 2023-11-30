<?php

namespace OidcAuth\Repository\Storage;

use Illuminate\Cache\Repository;
use OidcAuth\Contracts\Storage;

class CacheStorage implements Storage
{
    private string $prefix = 'oidc';

    public function __construct(protected Repository $repository)
    {

    }

    public function get($key): mixed
    {
        return $this->repository->get("$this->prefix.$key");
    }

    public function set($key, $value): void
    {
        $this->repository->set("$this->prefix.$key", $value);
    }
}
