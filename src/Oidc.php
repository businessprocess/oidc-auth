<?php

namespace OidcAuth;

class Oidc
{
    public static string $shortKey = 'st';

    public static $tokenRetrievalCallback;

    public static $userAuthenticationCallback;

    public static $userProviderCallback;

    public static function useShortKey(string $shortKey): void
    {
        self::$shortKey = $shortKey;
    }

    public static function getAccessTokenFromRequestUsing(callable $callback): void
    {
        static::$tokenRetrievalCallback = $callback;
    }

    public static function getAuthenticationUserUsing(callable $callback): void
    {
        static::$userAuthenticationCallback = $callback;
    }

    public static function getByProviderUsing(callable $callback): void
    {
        static::$userProviderCallback = $callback;
    }
}
