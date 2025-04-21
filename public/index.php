<?php
/**
 * PHP Sandbox application.
 *
 * @link https://github.com/ensostudio/php-sandbox
 */
use EnsoStudio\PhpSandbox\Actions;
use Slim\Factory\AppFactory;
use Slim\Middleware\ContentLengthMiddleware;

require __DIR__ . '/../vendor/autoload.php';

$application = AppFactory::create();

$application->addRoutingMiddleware();
$application->add(new ContentLengthMiddleware());
// This middleware should be added last. It will not handle any exceptions or errors for middleware added after it.
$application->addErrorMiddleware(true, false, false);

$application->post('/format', Actions\FormatAction::class);
$application->post('/execute', Actions\ExecuteAction::class);
$application->get('/autocomplete', Actions\AutoCompleteAction::class);
$application->get('/', Actions\IndexAction::class);

$application->run();
