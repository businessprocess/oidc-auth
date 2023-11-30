<?php

namespace OidcAuth\Service;

use OidcAuth\Contracts\Decoder;
use OidcAuth\Contracts\HttpClient;
use OidcAuth\Exceptions\UnauthorizedException;
use OidcAuth\Models\OidcUser as User;
use OidcAuth\Repository\Credential;
use OidcAuth\Repository\Payload;
use OidcAuth\Repository\TokenRepository;

class OidcService
{
    public static string $shortKey = 'token';

    public function __construct(
        protected HttpClient $client,
        protected TokenRepository $repository,
        protected Credential $credential,
        protected ?Decoder $decoder = null
    ) {
    }

    public static function getShortKey(): string
    {
        return self::$shortKey;
    }

    public static function setShortKey(string $shortKey): void
    {
        self::$shortKey = $shortKey;
    }

    public function serviceToken(): string
    {
        $jwt = $this->repository->jwt($this->credential->login());

        if (! $jwt) {
            return $this->serviceAuthorize()->token();
        }

        if ($this->check($jwt)) {
            return $jwt;
        }

        if ($rt = $this->repository->rt($jwt)) {
            return $this->reauthorize($jwt, $rt)->token();
        }

        return $this->serviceAuthorize()->token();
    }

    public function token(string $bptUserId): ?string
    {
        $jwt = $this->repository->jwt($bptUserId);

        if (! $jwt) {
            return null;
        }

        if ($this->check($jwt)) {
            return $jwt;
        }

        if ($rt = $this->repository->rt($jwt)) {
            return $this->reauthorize($jwt, $rt)->token();
        }

        return null;
    }

    public function tokenFromShort(string $sr): ?string
    {
        try {
            return $this->reauthorize(null, null, $sr)->token();
        } catch (\Exception $e) {
            return null;
        }
    }

    private function authorize(string $login, string $password, array $payload = [], int $ttl = null, $realm = Payload::REALM_USER): User
    {
        $response = $this->client->post('/authorize', array_filter(compact('login', 'password', 'realm', 'payload', 'ttl')))->throw();

        return $this->repository->update(new User($response->json()));
    }

    public function userAuthorize(string $login, string $password, array $payload = [], int $ttl = null): User
    {
        return $this->authorize($login, $password, $payload, $ttl);
    }

    public function serviceAuthorize(string $login = null, string $password = null, array $payload = [], int $ttl = null): User
    {
        $login ??= $this->credential->login();
        $password ??= $this->credential->password();

        if (! $login || ! $password) {
            throw new \InvalidArgumentException('Login and password is required');
        }

        return $this->authorize($login, $password, $payload, $ttl, Payload::REALM_SERVICE);
    }

    public function reauthorize(string $jwt = null, string $rt = null, string $st = null): User
    {
        $response = $this->client->get('/authorize', [], array_filter([
            'authorization' => $jwt,
            'authorization-rt' => $rt,
            'authorization-st' => $st,
        ]))->throw();

        return $this->repository->update(new User($response->json()));
    }

    public function check(string $jwt): Payload|bool
    {
        try {
            if ($this->decoder) {
                return new Payload($this->decoder->decode($jwt, $this->publicKey()));
            }
            $response = $this->client->get('/authorize/check', [], ['authorization' => $jwt])->throw();

            return new Payload($response->json('payload'));
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * @throws UnauthorizedException
     */
    public function short(?string $jwt): ?string
    {
        return $this->client->get('/authorize/short', [], ['authorization' => $jwt])->throw()->json('st');
    }

    /**
     * @throws UnauthorizedException
     */
    public function shortUser(?string $jwt, string $bptUserId): ?string
    {
        return $this->client->post('/authorize/short-bpt-user', compact('jwt', 'bptUserId'), ['authorization' => $jwt])
            ->throw()
            ->json('st');
    }

    public function publicKey(): string
    {
        if (! $key = $this->repository->publicKey()) {
            $key = $this->client->get('/public-key')->body();

            $this->repository->setPublicKey($key);
        }

        return $key;
    }

    public function alive(): bool
    {
        return $this->client->get('/utils/alive')->successful();
    }
}
