<?php

namespace App\Validator\Slack;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Expressive\Container\Exception\InvalidArgumentException;

class ValidateSlackToken
{
    /**
     * @var array
     */
    private $validTokens;

    public function __construct(array $validTokens)
    {
        $this->validTokens = $validTokens;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next)
    {
        $body  = $request->getParsedBody();
        $token = $body['token'] ?? "";
        if ($this->validTokens[$request->getUri()->getPath()] != $token) {
            throw new InvalidArgumentException("Invalid Slack Token");
        }

        return $next($request, $response, $error ?? null);
    }
}