<?php

namespace App\Pipeline;

use App\Service\Jira\JiraShowIssueCommand;
use App\Service\Slack\SendMessageToSlackUserViaResponseUrl;
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
        $pipeline->pipe($container->get(JiraShowIssueCommand::class));
        $pipeline->pipe(
            $container->build(SendMessageToSlackUserViaResponseUrl::class, ['message' => 'Processing Complete'])
        );

        return $pipeline;
    }
}
