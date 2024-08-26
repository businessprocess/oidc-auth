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
    public function __construct(
        protected HttpClient $client,
        protected TokenRepository $repository,
        protected Credential $credential,
        protected ?Decoder $decoder = null
    ) {}

    /**
     * @throws UnauthorizedException
     */
    public function serviceToken(): string
    {
        try {
            return $this->token($this->credential->login());
        } catch (UnauthorizedException $e) {
        }

        return $this->serviceAuthorize()->token();
    }

    /**
     * @throws UnauthorizedException
     */
    public function token(string $key): ?string
    {
        $jwt = $this->repository->jwt($key);

        if ($jwt) {
            if ($this->check($jwt)) {
                return $jwt;
            }

            if ($rt = $this->repository->rt($jwt)) {
                return $this->reauthorize($jwt, $rt)->token();
            }
        }

        throw new UnauthorizedException;
    }

    public function tokenFromShort(string $st): ?string
    {
        try {
            return $this->reauthorize(null, null, $st)->token();
        } catch (UnauthorizedException $e) {
            return null;
        }
    }

    public function userFromShort(string $st): ?User
    {
        try {
            return $this->reauthorize(null, null, $st);
        } catch (UnauthorizedException $e) {
            return null;
        }
    }

    protected function authorize(string $login, string $password, array $payload = [], ?int $ttl = null, $realm = Payload::REALM_SERVICE): User
    {
        $response = $this->client->post('/authorize', array_filter(compact('login', 'password', 'realm', 'payload', 'ttl')))->throw();

        return $this->repository->update(new User($response->json()));
    }

    public function userAuthorize(string $login, string $password, array $payload = [], ?int $ttl = null, $realm = null): User
    {
        $realm ??= Payload::getDefaultRealm();

        return $this->authorize($login, $password, $payload, $ttl, $realm);
    }

    public function serviceAuthorize(?string $login = null, ?string $password = null, array $payload = [], ?int $ttl = null): User
    {
        $login ??= $this->credential->login();
        $password ??= $this->credential->password();

        if (! $login || ! $password) {
            throw new \InvalidArgumentException('Login and password is required');
        }

        return $this->authorize($login, $password, $payload, $ttl);
    }

    protected function reauthorize(?string $jwt = null, ?string $rt = null, ?string $st = null): User
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
            $result = $this->decoder
                ? $this->decoder->decode($jwt, $this->publicKey())
                : $this->client->get('/authorize/check', [], ['authorization' => $jwt])->throw()->json('payload');

            $payload = new Payload($result);

            return $payload->isValidRealm() ? $payload : false;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * @throws UnauthorizedException
     */
    public function short(?string $jwt, array $payload = []): ?string
    {
        $method = empty($payload) ? 'get' : 'post';

        return $this->client->{$method}('/authorize/short', compact('payload'), ['authorization' => $jwt])->throw()->json('st');
    }

    /**
     * @throws UnauthorizedException
     */
    public function shortUser(?string $jwt, string $bptUserId, array $payload = []): ?string
    {
        return $this->client->post('/authorize/short-bpt-user', compact('jwt', 'bptUserId', 'payload'), ['authorization' => $jwt])
            ->throw()
            ->json('st');
    }

    protected function publicKey(): string
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
