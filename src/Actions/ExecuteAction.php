<?php

namespace EnsoStudio\PhpSandbox\Actions;

use ErrorException;
use Slim\Psr7\Request;
use Slim\Psr7\Response;
use Throwable;

/**
 * Application action executes PHP code.
 */
class ExecuteAction implements ActionInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(Request $request, Response $response): Response
    {
        $code = $request->getParsedBody()['code'];
        $code = \preg_replace('/^<\?(php)?\s*\r?\n/', '', \trim($code), 1);

        \ob_start();
        try {
            self::executeCode($code);
            $response->getBody()->write(\ob_get_clean() . '#php-sandbox-end-output#');
        } catch (Throwable $e) {
            $error = \get_class($e) . ': ' . \str_replace(['"', "\r", "\n"], ["'", '', ' '], $e->getMessage());
            $line = $e->getLine() + 1;
            foreach ($e->getTrace() as $data) {
                if (\str_ends_with($data['file'], 'eval()\'d code')) {
                    $line = $data['line'] + 1;
                    break;
                }
            }

            $response = $response->withHeader('X-Error', '"' . $error . '"; line=' . $line)
                ->withStatus(500);
            $response->getBody()->write(\ob_get_clean());
        }

        return $response;
    }

    /**
     * Execute code in clear context.
     */
    static private function executeCode(string $code)
    {
        \error_reporting(E_ALL);
        \ini_set('log_errors', 'Off');
        \ini_set('display_errors', 'On');
        \set_error_handler(
            static function ($code, $message, $file, $line) {
                throw new ErrorException($message, $code, $code, $file, $line);
            }
        );

        require \dirname(__DIR__) . DIRECTORY_SEPARATOR . 'bootstrap.php';

        eval($code);
    }
}
