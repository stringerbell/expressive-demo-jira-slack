<?php

namespace App\Validator;

use Psr\Http\Message\ServerRequestInterface;
use Zend\Expressive\Container\Exception\InvalidArgumentException;
use Zend\Stratigility\Http\ResponseInterface;

class ValidateBody
{
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next)
    {
        $body = $request->getParsedBody();
        if (!$body) {
            throw new InvalidArgumentException("Invalid Body");
        }
        return $next($request, $response);
    }
}
