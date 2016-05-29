<?php

namespace App\Pipeline;

use App\Validator\ValidateBody;
use Interop\Container\ContainerInterface;
use Zend\Stratigility\MiddlewarePipe;

class SlackJiraPipeline
{
    public function __invoke(ContainerInterface $container)
    {
        $pipeline = new MiddlewarePipe();
        $pipeline->pipe($container->get(ValidateBody::class));

        return $pipeline;
    }
}
