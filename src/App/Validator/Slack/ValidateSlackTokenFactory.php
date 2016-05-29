<?php

namespace App\Validator\Slack;

use Interop\Container\ContainerInterface;

class ValidateSlackTokenFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $validTokens = $container->get('config')['slack_config']['tokens'] ?? [];

        return new ValidateSlackToken($validTokens);
    }
}
