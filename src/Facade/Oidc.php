<?php

namespace OidcAuth\Facade;

use Illuminate\Support\Facades\Facade;
use OidcAuth\Models\OidcUser as User;
use OidcAuth\Repository\Payload;
use OidcAuth\Service\OidcService;

/**
 * @method static string serviceToken()
 * @method static string token(string $bptUserId)
 * @method static User userAuthorize(string $login, string $password, array $payload = [], int $ttl = null, $realm = null)
 * @method static User serviceAuthorize(string $login = null, string $password = null, array $payload = [], int $ttl = null)
 * @method static Payload|bool check(string $jwt)
 * @method static string|null short(?string $jwt, array $payload = [])
 * @method static string|null shortUser(?string $jwt, string $bptUserId, array $payload = [])
 * @method static string|null tokenFromShort(string $st)
 * @method static User|null userFromShort(string $st)
 * @method static User|null refreshToken(string $jwt, ?string $rt = null)
 * @method static bool alive()
 *
 * @see OidcService
 */
class Oidc extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return OidcService::class;
    }
}
