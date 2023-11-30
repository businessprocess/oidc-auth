<?php

namespace OidcAuth\Repository\Storage;

use OidcAuth\Contracts\Storage;

class ArrayStorage implements Storage
{
    public iterable $storage;

    public function __construct(iterable $storage = [])
    {
        $this->storage = $storage;
    }

    public function get($key): mixed
    {
        return $this->storage[$key] ?? null;
    }

    public function set($key, $value): void
    {
        if (isset($this->storage[$key])) {
            $this->storage[$key] = $value;
        } else {
            $this->storage = array_merge($this->storage, [$key => $value]);
        }
    }
}
