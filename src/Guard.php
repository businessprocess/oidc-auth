<?php

namespace OidcAuth;

use Illuminate\Contracts\Auth\Factory as AuthFactory;
use Illuminate\Http\Request;
use OidcAuth\Events\TokenAuthenticated;
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

        if ($token = $request->header('authorization')) {
            $model = $this->getModel();

            $payload = $this->service->check($token);

            if ($payload || $model) {
                $user = $model::find($payload->getBptUserId());

                event(new TokenAuthenticated($token));

                return $user;
            }
        }
    }

    protected function getModel(): mixed
    {
        if (is_null($this->provider)) {
            return null;
        }

        return config("auth.providers.{$this->provider}.model");
    }
}
