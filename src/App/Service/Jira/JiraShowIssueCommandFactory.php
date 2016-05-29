<?php

namespace App\Service\Jira;

use App\Client\JiraClient;
use App\Client\SlackClient;
use Interop\Container\ContainerInterface;

class JiraShowIssueCommandFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $jiraClient  = $container->get(JiraClient::class);
        $slackClient = $container->get(SlackClient::class);
        $jiraUrl     = $container->get('config')['jira_config']['base_uri'] ?? '';

        return new JiraShowIssueCommand($jiraClient, $slackClient, $jiraUrl);
    }
}
