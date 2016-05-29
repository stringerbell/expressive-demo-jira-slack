<?php

namespace App\Service\Slack;

use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class SendMessageToSlackUserViaResponseUrl
{
    /**
     * @var Client
     */
    private $slackClient;
    /**
     * @var string
     */
    private $message;

    public function __construct(Client $slackClient, string $message)
    {
        $this->slackClient = $slackClient;
        $this->message     = $message;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next)
    {
        $body         = $request->getParsedBody();
        $responseBody = [
            'text'          => $this->message,
            'response_type' => $body['response_type'] ?? 'ephemeral',
        ];
        $this->slackClient->post($body['response_url'], ['body' => json_encode($responseBody)]);

        return $next($request, $response, $error ?? null);
    }
}
