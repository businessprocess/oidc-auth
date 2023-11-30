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
    public function __construct(protected AuthFactory $auth, protected OidcService $service, protected $provider = null)
    {
    }

    public function __invoke(Request $request): mixed
    {
        if ($user = $this->auth->guard('web')->user()) {
            return $user;
        }
        $token = $request->header('authorization');

        if ($sr = $request->get(OidcService::getShortKey())) {
            $token = $this->service->tokenFromShort($sr);
        }

        if (! $token) {
            return null;
        }

        if (! $payload = $this->service->check($token)) {
            return null;
        }

        if ($model = $this->getModel()) {
            $user = $model::find($payload->getBptUserId());

            event(new TokenAuthenticated($token));

            return $this->supportsTokens($user) ? $user->withToken($token) : $user;
        }

        return (new User())->withToken($token)->setPayload($payload);
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
