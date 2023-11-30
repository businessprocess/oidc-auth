<?php

namespace OidcAuth\Contracts;

interface Storage
{
    public function get($key): mixed;

    public function set($key, $value): void;
}
