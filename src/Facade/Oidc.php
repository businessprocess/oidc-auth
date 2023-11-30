<?php

namespace OidcAuth\Facade;

use Illuminate\Support\Facades\Facade;
use OidcAuth\Repository\TokenRepository;
use OidcAuth\Service\OidcService;

/**
 * @method static string serviceToken()
 * @method static string token(string $bptUserId)
 * @method static TokenRepository userAuthorize(string $login, string $password, array $payload = [], int $ttl = null)
 * @method static TokenRepository serviceAuthorize(string $login = null, string $password = null, array $payload = [], int $ttl = null)
 * @method static TokenRepository reauthorize(string $jwt = null, string $rt = null, string $st = null)
 * @method static TokenRepository check(string $jwt)
 * @method static string short(string $jwt)
 * @method static string shortUser(string $jwt, string $bptUserId)
 * @method static string tokenFromShort(string $sr)
 * @method static string publicKey()
 * @method static bool alive()
 */
class Oidc extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return OidcService::class;
    }
}
