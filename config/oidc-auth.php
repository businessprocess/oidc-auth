<?php

return [
    'url' => env('OIDC_AUTH_URL'),
    'credentials' => [
        'login' => env('OIDC_AUTH_LOGIN'),
        'password' => env('OIDC_AUTH_PASSWORD'),
    ],
    'realm' => env('OIDC_AUTH_REALM'),
];
