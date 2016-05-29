<?php

namespace App\Client;

use GuzzleHttp\Client;
use Interop\Container\ContainerInterface;

class JiraClient
{
    public function __invoke(ContainerInterface $container)
    {
        $config  = $container->get('config')['jira_config'] ?? [];
        $auth    = $config['auth'] ?? [];
        $baseUri = $config['base_uri'] ?? '';

        return new Client(
            [
                'base_uri' => $baseUri,
                'auth'     => [($auth['user'] ?? ''), ($auth['password'] ?? '')],
                'headers'  => [
                    'Content-Type' => 'application/json',
                ],
            ]
        );
    }
}
