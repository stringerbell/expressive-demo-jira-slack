<?php

namespace App\Service\Jira;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\UriTemplate;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class JiraShowIssueCommand
{
    /**
     * @var Client
     */
    private $jiraClient;
    /**
     * @var Client
     */
    private $slackClient;
    /**
     * @var string
     */
    private $jiraUrl;

    public function __construct(Client $jiraClient, Client $slackClient, string $jiraUrl)
    {
        $this->jiraClient  = $jiraClient;
        $this->slackClient = $slackClient;
        $this->jiraUrl     = $jiraUrl;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next)
    {
        $body = $request->getParsedBody();
        if ($body['args'][0] !== "show") {
            return $next($request, $response);
        }
        $jobs        = array_reverse(array_slice($body['args'], 1));
        $uriTemplate = new UriTemplate();
        $uri         = $uriTemplate->expand(
            'rest/api/2/search?jql=key in ({issueKeys*})&expand=editmeta&fields=customfield_10024&fields=summary'
            . '&fields=creator&fields=assignee&fields=issuetype&fields=priority&fields=status&fields=resolution',
            [
                'issueKeys' => $jobs,
            ]
        );
        try {
            $json         = json_decode($this->jiraClient->get($uri)->getBody()->getContents(), true);
            $attachments  = $this->prepareSlackAttachments($json);
            $responseBody = [
                'text'          => "Search Results for `{$body['command']} {$body['text']}`",
                'response_type' => $body['response_type'] ?? 'ephemeral',
                'attachments'   => $attachments,
            ];
            $this->slackClient->post($body['response_url'], ['body' => json_encode($responseBody)]);
        } catch (ClientException $e) {
            $error = $e->getMessage();
            $this->slackClient->post(
                $body['response_url'],
                [
                    'body' => json_encode(
                        [
                            'text' => "Running `{$body['command']} {$body['text']}` didn't work. Got "
                                . $e->getCode() . " for an HTTP response",
                        ]
                    ),
                ]
            );
        }

        return $next($request, $response, $error ?? null);
    }

    private function prepareSlackAttachments($json)
    {
        $results  = [];
        $jobs     = $json['issues'] ?? [];
        $colorMap = [
            'Blocker'  => '#ec0909', // bright red
            'Critical' => '#d9534f', // red
            'Major'    => '#f0ad4e', // orange
            'Medium'   => '#5bc0de', // blue
            'Trivial'  => '#c7ead4', // light-green
        ];
        foreach ($jobs as $index => $job) {
            $priority = $job['fields']['priority']['name'] ?? '¯\_(ツ)_/¯';
            $status   = $job['fields']['status']['name'] ?? '¯\_(ツ)_/¯';
            $type     = $job['fields']['issuetype']['name'] ?? '¯\_(ツ)_/¯';
            $creator  = $job['fields']['creator']['displayName'] ?? 'no one';
            $assignee = $job['fields']['assignee']['displayName'] ?? 'no one';
            $blocked  = ($job['fields']['customfield_10024']['value'] == "Yes" ? " that is currently *Blocked*" : '');

            $results[$index]['title']      = "{$job['key']} - " . ($job['fields']['summary'] ?? '');
            $results[$index]['title_link'] = "{$this->jiraUrl}/browse/{$job['key']}";
            $results[$index]['fallback']   = $results[$index]['title'];
            $results[$index]['color']      = $colorMap[$job['fields']['priority']['name'] ?? null] ?? '#205081';
            $results[$index]['text']       = "A *{$priority}* *{$type}* marked as *'{$status}'*,"
                . " created by *{$creator}* assigned to *{$assignee}* {$blocked}";
            $results[$index]['mrkdwn_in']  = ['text'];
        }

        return $results;
    }
}
