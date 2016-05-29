<?php

namespace AppTest\Validator\Slack;

use App\Validator\Slack\ValidateSlackToken;
use App\Validator\Slack\ValidateSlackTokenFactory;
use Interop\Container\ContainerInterface;

class ValidateSlackTokenFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function itWillDoTheNeedful()
    {
        $container = $this->prophesize(ContainerInterface::class);
        $factory   = new ValidateSlackTokenFactory();
        $container->get('config')->shouldBeCalled()->willReturn([]);
        $result = $factory($container->reveal());
        $this->assertInstanceOf(ValidateSlackToken::class, $result);
    }
}
