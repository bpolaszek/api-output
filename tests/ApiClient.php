<?php

declare(strict_types=1);

namespace App\Tests;

use ApiPlatform\Symfony\Bundle\Test\Client;
use Symfony\Contracts\HttpClient\ResponseInterface;

use function array_replace;
use function is_string;

final class ApiClient
{
    public function __construct(
        public Client $client,
    ) {
    }

    /**
     * @param array<string, mixed> $options
     */
    public function get(string $url, array $options = []): ResponseInterface
    {
        return $this->request('GET', $url, $options);
    }

    /**
     * @param array<string, mixed> $data
     * @param array<string, mixed> $options
     */
    public function post(string $url, array|string $data = [], array $options = []): ResponseInterface
    {
        if (is_string($data)) {
            $options['body'] = $data;
        } else {
            $options['json'] = $data;
        }

        return $this->request('POST', $url, $options);
    }

    /**
     * @param array<string, mixed> $data
     * @param array<string, mixed> $options
     */
    public function put(string $url, array $data = [], array $options = []): ResponseInterface
    {
        $options['json'] = $data;

        return $this->request('PUT', $url, $options);
    }

    /**
     * @param array<string, mixed> $data
     * @param array<string, mixed> $options
     */
    public function patch(string $url, array $data = [], array $options = []): ResponseInterface
    {
        $options['json'] = $data;
        $options['headers'] = array_replace(
            ['Content-Type' => 'application/merge-patch+json'],
            $options['headers'] ?? [],
        );

        return $this->request('PATCH', $url, $options);
    }

    /**
     * @param array<string, mixed> $options
     */
    public function delete(string $url, array $options = []): ResponseInterface
    {
        return $this->request('DELETE', $url, $options);
    }

    /**
     * @param array<string, mixed> $options
     */
    private function request(string $method, string $url, array $options): ResponseInterface
    {
        $this->client->request($method, $url, $options);

        /** @var ResponseInterface $response */
        $response = $this->client->getResponse();

        return $response;
    }
}
