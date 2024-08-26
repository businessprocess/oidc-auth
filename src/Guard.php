<?php

namespace OidcAuth;

use Illuminate\Contracts\Auth\Factory as AuthFactory;
use Illuminate\Http\Request;
use OidcAuth\Events\TokenAuthenticated;
use OidcAuth\Models\OidcUser as User;
use OidcAuth\Service\OidcService;

class Guard
{
    /**
     * Create a new guard instance.
     *
     * @param  null  $provider
     */
    public function __construct(protected AuthFactory $auth, protected OidcService $service, protected $provider = null) {}

    public function __invoke(Request $request): mixed
    {
        if ($user = $this->auth->guard('web')->user()) {
            return $user;
        }

        if (! $token = $this->getTokenFromRequest($request)) {
            return null;
        }

        if (! $payload = $this->service->check($token)) {
            return null;
        }

        $user = (new User)->withToken($token)->setPayload($payload);

        event(new TokenAuthenticated($user));

        if (is_callable(Oidc::$userAuthenticationCallback)) {
            return (Oidc::$userAuthenticationCallback)($user);
        }

        if ($model = $this->getModel()) {
            if (is_callable(Oidc::$userProviderCallback)) {
                return (Oidc::$userProviderCallback)($model, $payload);
            }

            $user = $model::find($payload->getKey());

            return $user && $this->supportsTokens($user) ? $user->withToken($token) : $user;
        }

        return $user;
    }

    protected function getTokenFromRequest($request)
    {
        if (is_callable(Oidc::$tokenRetrievalCallback)) {
            return (Oidc::$tokenRetrievalCallback)($request);
        }

        $token = $request->header('authorization');

        if ($st = $request->get(Oidc::$shortKey)) {
            $token = $this->service->tokenFromShort($st);
        }

        return $token;
    }

    protected function supportsTokens($user = null): bool
    {
        return $user && in_array(HasJwtToken::class, class_uses_recursive(
            get_class($user)
        ));
    }

    protected function getModel(): mixed
    {
        if (is_null($this->provider)) {
            return null;
        }

        return config("auth.providers.{$this->provider}.model");
    }
}
