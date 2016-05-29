<?php

namespace App\Validator\Slack;

use App\Utility\ArgvParser;
use GuzzleHttp\Client;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Console\Exception\RuntimeException;
use Zend\Console\Getopt;
use Zend\Stratigility\Http\ResponseInterface;

class ParseSlackJiraInput
{
    /**
     * @var Client
     */
    private $slackClient;

    public function __construct(Client $slackClient)
    {
        $this->slackClient = $slackClient;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next)
    {
        $body    = $request->getParsedBody();
        $text    = $body['text'] ?? '';
        $args    = ArgvParser::parseString($text);
        $_SERVER['argv'][0] = "/jira show issuesKey(s)[]"; // will show up as usage message
        // you have to set register_argc_argv => On for this to work with Getopt, or you can dump things into $_SERVER['argv']
        $opts = new Getopt(
            [
                'p|public' => 'publicly post option',
            ],
            $args
        );
        try {
            if (!$opts->getOptions() && count($opts->getArguments()) == 1) {
                throw new RuntimeException("Invalid arguments", $opts->getUsageMessage());
            }
            if (!$opts->getRemainingArgs()) {
                throw new RuntimeException("Invalid arguments", $opts->getUsageMessage());
            }
            $body['args'] = $opts->getRemainingArgs();
            // set options in body for later
            $body['response_type'] = $opts->public ? 'in_channel' : 'ephemeral';
        } catch (RuntimeException $e) {
            $responseBody = [
                'text' => "Tried running: `{$body['command']} {$body['text']}` \n" . $e->getUsageMessage(),
            ];
            $this->slackClient->post(
                $body['response_url'] ?? '',
                [
                    'body'    => json_encode($responseBody),
                    'headers' => [
                        'Content-Type' => 'application/json',
                    ],
                ]
            );

            $error = $e->getUsageMessage();
        }

        return $next($request->withParsedBody($body), $response, $error ?? null);
    }
}
