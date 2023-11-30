<?php

namespace OidcAuth\Service;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use OidcAuth\Contracts\Decoder;

class JwtDecoder implements Decoder
{
    /**
     * @throws \Exception
     */
    public function decode(string $jwt, string $key, string $algorithm = 'HS256'): mixed
    {
        return JWT::decode($jwt, new Key($key, 'HS256'));
    }
}
