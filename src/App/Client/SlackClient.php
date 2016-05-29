<?php

namespace App\Client;

use GuzzleHttp\Client;
use Interop\Container\ContainerInterface;

class SlackClient
{
    public function __invoke(ContainerInterface $container)
    {
        return new Client(['headers' => ['Content-Type' => 'application/json']]);
    }
}
