<?php

namespace OidcAuth\Service;

use OidcAuth\Contracts\Decoder;
use OidcAuth\Contracts\HttpClient;
use OidcAuth\Repository\Credential;
use OidcAuth\Repository\Payload;
use OidcAuth\Repository\TokenRepository;

class OidcService
{
    public function __construct(
        protected HttpClient $client,
        protected TokenRepository $repository,
        protected Credential $credential,
        protected ?Decoder $decoder = null
    ) {
    }

    public function serviceToken(): string
    {
        $jwt = $this->repository->jwt($this->credential->login());

        if (! $jwt) {
            return $this->serviceAuthorize()->jwt($this->credential->login());
        }

        if ($this->check($jwt)) {
            return $jwt;
        }

        if ($rt = $this->repository->rt($jwt)) {
            return $this->reauthorize($jwt, $rt)->jwt($this->credential->login());
        }

        return $this->serviceAuthorize()->jwt($this->credential->login());
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
            return $this->reauthorize($jwt, $rt)->jwt($bptUserId);
        }

        return null;
    }

    private function authorize(string $login, string $password, array $payload = [], int $ttl = null, $realm = Payload::REALM_USER): TokenRepository
    {
        $response = $this->client->post('/authorize', array_filter(compact('login', 'password', 'realm', 'payload', 'ttl')))->throw();

        return $this->repository->fill($response->json());
    }

    public function userAuthorize(string $login, string $password, array $payload = [], int $ttl = null): TokenRepository
    {
        return $this->authorize($login, $password, $payload, $ttl);
    }

    public function serviceAuthorize(string $login = null, string $password = null, array $payload = [], int $ttl = null): TokenRepository
    {
        $login ??= $this->credential->login();
        $password ??= $this->credential->password();

        if (! $login || ! $password) {
            throw new \InvalidArgumentException('Login and password is required');
        }

        return $this->authorize($login, $password, $payload, $ttl, Payload::REALM_SERVICE);
    }

    public function reauthorize(string $jwt = null, string $rt = null, string $st = null): TokenRepository
    {
        $response = $this->client->get('/authorize', [], [
            'jwt' => $jwt,
            'rt' => $rt,
            'st' => $st,
        ]);

        return $this->repository->fill($response->json());
    }

    public function check(string $jwt): Payload|bool
    {
        try {
            if ($this->decoder) {
                return new Payload($this->decoder->decode($jwt, $this->publicKey()));
            }
            $response = $this->client->get('/authorize/check', [], compact('jwt'))->throw();

            return new Payload($response->json('payload'));
        } catch (\Exception $e) {
            return false;
        }
    }

    public function short(string $bptUserId): string
    {
        $jwt = $this->repository->jwt($bptUserId);

        return $this->client->get('/authorize/short', [], compact('jwt'))->json('st');
    }

    public function shortUser(string $bptUserId): string
    {
        $jwt = $this->repository->jwt($bptUserId);

        return $this->client->post('/authorize/short-bpt-user', compact('jwt', 'bptUserId'), compact('jwt'))
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
