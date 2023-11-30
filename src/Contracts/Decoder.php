<?php

namespace OidcAuth\Contracts;

interface Decoder
{
    public function decode(string $jwt, string $key, string $algorithm = 'HS256'): mixed;
}
