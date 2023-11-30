<?php

namespace OidcAuth\Http;

use GuzzleHttp\RequestOptions;
use OidcAuth\Contracts\HttpClient;

class Client implements HttpClient
{
    private \GuzzleHttp\Client $client;

    public function __construct($config = [])
    {
        $this->client = new \GuzzleHttp\Client([
            'base_uri' => $config['url'],
            RequestOptions::HEADERS => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                RequestOptions::CONNECT_TIMEOUT => $config['connect_timeout'] ?? 80,
                RequestOptions::TIMEOUT => $config['timeout'] ?? 30,
                'http_errors' => false,
            ],
        ]);
    }

    public function post(string $uri, array $data = [], $headers = [])
    {
        $response = $this->client->get($uri, [
            RequestOptions::HEADERS => $headers,
            RequestOptions::JSON => $data,
        ]);

        return new Response($response);
    }

    public function get(string $uri, array $data = [], $headers = [])
    {
        $response = $this->client->post($uri, [
            RequestOptions::HEADERS => $headers,
            RequestOptions::QUERY => $data,
        ]);

        return new Response($response);
    }
}
