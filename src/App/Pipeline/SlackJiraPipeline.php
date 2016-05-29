<?php

namespace App\Pipeline;

use App\Validator\Slack\ParseSlackJiraInput;
use App\Validator\Slack\ValidateSlackToken;
use App\Validator\ValidateBody;
use Interop\Container\ContainerInterface;
use Zend\Stratigility\MiddlewarePipe;

class SlackJiraPipeline
{
    public function __invoke(ContainerInterface $container)
    {
        $pipeline = new MiddlewarePipe();
        $pipeline->pipe($container->get(ValidateBody::class));
        $pipeline->pipe($container->get(ValidateSlackToken::class));
        $pipeline->pipe($container->get(ParseSlackJiraInput::class));

        return $pipeline;
    }
}
