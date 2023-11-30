<?php

namespace OidcAuth\Contracts;

use OidcAuth\Http\Response;

interface HttpClient
{
    /**
     * @return Response
     */
    public function post(string $uri, array $data = [], array $headers = []);

    /**
     * @return Response
     */
    public function get(string $uri, array $data = [], array $headers = []);
}
