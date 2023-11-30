<?php

namespace OidcAuth;

use App\Models\User;
use Illuminate\Auth\RequestGuard;
use Illuminate\Contracts\Auth\Factory;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
use OidcAuth\Contracts\Decoder;
use OidcAuth\Contracts\Storage;
use OidcAuth\Http\Client;
use OidcAuth\Repository\Credential;
use OidcAuth\Repository\Storage\CacheStorage;
use OidcAuth\Repository\TokenRepository;
use OidcAuth\Service\OidcService;

class OidcAuthServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        config([
            'auth.guards.oidc' => array_merge([
                'driver' => 'oidc',
                'provider' => null,
                'decoder' => null,
            ], config('auth.guards.oidc', [])),
        ]);

        if (! app()->configurationIsCached()) {
            $this->mergeConfigFrom(__DIR__.'/../config/oidc-auth.php', 'oidc');
        }

        $this->app->bind(Storage::class, function (Application $app) {
            return $app->make(CacheStorage::class);
        });

        $this->app->bind(Decoder::class, function (Application $app) {
            $class = $app['config']->get('auth.guards.oidc.decoder');
            if ($class instanceof Decoder) {
                return new $class;
            }

            return null;
        });

        $this->app->bind(OidcService::class, function (Application $app) {
            $config = $app['config']->get('oidc-auth');

            return new OidcService(
                new Client($config),
                $app->make(TokenRepository::class),
                new Credential($config['credentials']),
                $app->make(Decoder::class),
            );
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Auth::resolved(function ($auth) {
            $auth->extend('oidc', function ($app, $name, array $config) use ($auth) {
                return tap($this->createGuard($auth, $app->make(OidcService::class), $config), function ($guard) {
                    app()->refresh('request', $guard, 'setRequest');
                });
            });
        });

        $this->configureMacro();
    }

    private function configureMacro(): void
    {
        $provider = $this->app['config']->get('auth.guards.oidc.provider');
        $model = $this->app['config']->get("auth.providers.{$provider}.model");

        if ($model instanceof User) {
            User::macro('token', function () {
                return app()->make(OidcService::class)->token($this->getKey());
            });
        }
    }

    /**
     * Register the guard.
     */
    protected function createGuard(Factory $auth, OidcService $service, array $config): RequestGuard
    {
        return new RequestGuard(
            new Guard($auth, $service, $config['provider']),
            request(),
            $auth->createUserProvider($config['provider'] ?? null)
        );
    }
}
