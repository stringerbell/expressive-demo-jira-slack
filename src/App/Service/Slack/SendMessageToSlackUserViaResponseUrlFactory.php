<?php

namespace App\Service\Slack;

use App\Client\SlackClient;
use Interop\Container\ContainerInterface;

class SendMessageToSlackUserViaResponseUrlFactory
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = [])
    {
        $defaults = [
            'message' => 'You sent a message, but forgot to replace the default! Go you! 
            You should probably use `build`',
        ];
        $options += $defaults;
        $slackClient = $container->get(SlackClient::class);

        return new SendMessageToSlackUserViaResponseUrl($slackClient, $options['message']);
    }
}
