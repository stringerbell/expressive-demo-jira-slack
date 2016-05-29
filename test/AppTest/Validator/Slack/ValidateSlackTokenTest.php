<?php

namespace AppTest\Validator\Slack;

use App\Validator\Slack\ValidateSlackToken;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;
use Zend\Expressive\Container\Exception\InvalidArgumentException;

class ValidateSlackTokenTest extends \PHPUnit_Framework_TestCase
{
    private $validTokens;
    private $validateSlackToken;

    public function setUp()
    {
        $this->validTokens = [
            '/route1' => 'validToken',
            '/route2' => 'anotherToken'
        ];
        $this->validateSlackToken = new ValidateSlackToken($this->validTokens);
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     */
    public function itWillThrowForInvalidSlackToken()
    {
        /** @var ServerRequestInterface $request */
        $request = $this->prophesize(ServerRequestInterface::class);
        /** @var ResponseInterface $response */
        $response = $this->prophesize(ResponseInterface::class);
        $next     = function ($request, $response, $next) {
            return $next;
        };
        $body =[
            'token' => 'invalidToken'
        ];
        $uriInterface = $this->prophesize(UriInterface::class);
        $request->getUri()->shouldBeCalled()->willReturn($uriInterface);
        $uriInterface->getPath()->shouldBeCalled()->willReturn('/route1');
        $request->getParsedBody()->shouldBeCalled()->willReturn($body);
        $this->validateSlackToken->__invoke($request->reveal(), $response->reveal(), $next);
    }

    /**
     * @test
     */
    public function itWillNotThrowForValidSlackToken()
    {
        /** @var ServerRequestInterface $request */
        $request = $this->prophesize(ServerRequestInterface::class);
        /** @var ResponseInterface $response */
        $response = $this->prophesize(ResponseInterface::class);
        $next     = function ($request, $response) {
            return $response;
        };
        $body =[
            'token' => 'validToken'
        ];
        $uriInterface = $this->prophesize(UriInterface::class);
        $request->getUri()->shouldBeCalled()->willReturn($uriInterface);
        $uriInterface->getPath()->shouldBeCalled()->willReturn('/route1');
        $request->getParsedBody()->shouldBeCalled()->willReturn($body);
        $result = $this->validateSlackToken->__invoke($request->reveal(), $response->reveal(), $next);
        $this->assertInstanceOf(ResponseInterface::class, $result);
    }
}
