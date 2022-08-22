<?php
/**
 * PHP Sandbox application.
 *
 * @link https://github.com/ensostudio/php-sandbox
 */
use EnsoStudio\PhpSandbox\Actions;
use Slim\Factory\AppFactory;
use Slim\Middleware\ContentLengthMiddleware;

error_reporting(E_ALL);
ini_set('log_errors', 'Off');
ini_set('display_errors', 'On');
set_error_handler(
    static function ($code, $message, $file, $line) {
        throw new ErrorException($message, $code, $code, $file, $line);
    }
);

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
