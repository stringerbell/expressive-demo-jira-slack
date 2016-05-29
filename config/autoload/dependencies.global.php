<?php
use App\Client\SlackClient;
use App\Pipeline\SlackJiraPipeline;
use App\Validator\Slack\ParseSlackJiraInput;
use App\Validator\Slack\ParseSlackJiraInputFactory;
use App\Validator\Slack\ValidateSlackToken;
use App\Validator\Slack\ValidateSlackTokenFactory;
use App\Validator\ValidateBody;
use Zend\Expressive\Application;
use Zend\Expressive\Container\ApplicationFactory;
use Zend\Expressive\Helper;

return [
    // Provides application-wide services.
    // We recommend using fully-qualified class names whenever possible as
    // service names.
    'dependencies' => [
        // Use 'invokables' for constructor-less services, or services that do
        // not require arguments to the constructor. Map a service name to the
        // class name.
        'invokables' => [
            // Fully\Qualified\InterfaceName::class => Fully\Qualified\ClassName::class,
            Helper\ServerUrlHelper::class => Helper\ServerUrlHelper::class,
            ValidateBody::class           => ValidateBody::class,
        ],
        // Use 'factories' for services provided by callbacks/factory classes.
        'factories'  => [
            Application::class         => ApplicationFactory::class,
            Helper\UrlHelper::class    => Helper\UrlHelperFactory::class,
            SlackJiraPipeline::class   => SlackJiraPipeline::class,
            ValidateSlackToken::class  => ValidateSlackTokenFactory::class,
            ParseSlackJiraInput::class => ParseSlackJiraInputFactory::class,
            SlackClient::class         => SlackClient::class,
        ],
    ],
];
