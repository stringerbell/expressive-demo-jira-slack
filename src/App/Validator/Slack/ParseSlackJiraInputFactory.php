<?php

namespace App\Validator\Slack;

use App\Client\SlackClient;
use Interop\Container\ContainerInterface;

class ParseSlackJiraInputFactory
{
    public function __invoke(ContainerInterface $container)
    {
        return new ParseSlackJiraInput($container->get(SlackClient::class));
    }
}
