# oidc-auth
JWT token auth service

![PHP 8.x](https://img.shields.io/badge/PHP-%5E8.0-blue)
[![Laravel 8.x](https://img.shields.io/badge/Laravel-8.x-orange.svg)](http://laravel.com)
[![Yii 2.x](https://img.shields.io/badge/Yii-2.x-orange)](https://www.yiiframework.com/doc/guide/2.0/ru)
![Latest Stable Version](https://poser.pugx.org/businessprocess/oidc-auth/v/stable)
![Release date](https://img.shields.io/github/release-date/businessprocess/oidc-auth)
![Release Version](https://img.shields.io/github/v/release/businessprocess/oidc-auth)
![Total Downloads](https://poser.pugx.org/businessprocess/oidc-auth/downloads)
![Pull requests](https://img.shields.io/bitbucket/pr/businessprocess/oidc-auth)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=plastic-square)](LICENSE)
![Stars](https://img.shields.io/github/stars/businessprocess/oidc-auth?style=social)

Notification messenger channel to Laravel FrameWork v6.0 and above.

## Installation
The recommended way to install package is through
[Composer](http://getcomposer.org).

```bash
composer require businessprocess/oidc-auth
```

## Usage Laravel

Use middleware 'auth:oidc'
```php
\Illuminate\Support\Facades\Route::middleware(['auth:oidc'])->group(fn() => {

})

```

Configuration setting

Add to auth config file
```php
//Guard
    [
        'driver' => 'oidc',
        'provider' => null, // if null return OidcUser model 
        'decoder' => null, // Jwt token decoder (JwtDecoder), if null use service decoder
    ]
```

Configuration model

```php
class User extends Authenticatable
{
    use \OidcAuth\HasJwtToken;
}
```


#### Available Methods

| Methods          | Description                      | Return value | 
|------------------|----------------------------------|--------------|
| serviceToken     | Get service token                | string       |
| token            | Get user token                   | string       |
| userAuthorize    | Authorize user by credentials    | OidcUser     |
| serviceAuthorize | Authorize service by credentials | OidcUser     |
| reauthorize      | Reauthorize by refresh token     | OidcUser     |
| check            | Validate token                   | Payload,bool |
| short            | Get service short token          | string       |
| shortUser        | Get user short token             | string       |
| tokenFromShort   | Get jwt token from short token   | string       |
| publicKey        | Get public key                   | string       |
| alive            | Check is node is alive           | bool         |
