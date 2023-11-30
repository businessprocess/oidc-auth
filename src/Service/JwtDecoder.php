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
    public function decode(string $jwt, string $key, string $algorithm = 'RS256'): array
    {
        return (array) JWT::decode($jwt, new Key($key, 'RS256'));
    }
}
