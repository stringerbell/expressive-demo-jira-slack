<?php

namespace AppTest\Validator;

use App\Validator\ValidateBody;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Expressive\Container\Exception\InvalidArgumentException;
use Zend\Stratigility\Http\ResponseInterface;

class ValidateBodyTest extends \PHPUnit_Framework_TestCase
{
    private $validateBody;

    public function setUp()
    {
        $this->validateBody = new ValidateBody();
    }

    /**
     * @test
     * * @expectedException InvalidArgumentException
     */
    public function itWillThrowAnExceptionWhenBodyIsInvalid()
    {
        /** @var ServerRequestInterface $request */
        $request = $this->prophesize(ServerRequestInterface::class);
        /** @var ResponseInterface $response */
        $response = $this->prophesize(ResponseInterface::class);
        $next     = function ($request, $response) {
            return $response;
        };
        $request->getParsedBody()->shouldBeCalled()->willReturn([]);
        $result   = $this->validateBody->__invoke($request->reveal(), $response->reveal(), $next);
    }

    /**
     * @test
     */
    public function itWillNotThrowAnExceptionForValidBody()
    {
        /** @var ServerRequestInterface $request */
        $request = $this->prophesize(ServerRequestInterface::class);
        /** @var ResponseInterface $response */
        $response = $this->prophesize(ResponseInterface::class);
        $next     = function ($request, $response) {
            return $response;
        };
        $request->getParsedBody()->shouldBeCalled()->willReturn(["foo" => "bar"]);
        $result = $this->validateBody->__invoke($request->reveal(), $response->reveal(), $next);
        $this->assertInstanceOf(ResponseInterface::class, $result);
    }
}
