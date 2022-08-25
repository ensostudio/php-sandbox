<?php

namespace EnsoStudio\PhpSandbox\Actions;

use Slim\Exception\HttpInternalServerErrorException;
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
        $code = \preg_replace('/^<\?(php)?\r?\n/', '', \trim($code), 1);

        \ob_start();
        try {
            require __DIR__ . '/../bootstrap.php';
            eval($code);
            $response->getBody()->write(\ob_get_clean() . '#php-sandbox-end-output#');
        } catch (Throwable $e) {
            $msg = \get_class($e) . ': ' . \str_replace(['"', "\r", "\n"], ["'", '', ' '], $e->getMessage());
            $line = $e->getLine() + 1;
            foreach ($e->getTrace() as $trace) {
                if (\str_ends_with($trace['file'], 'eval()\'d code')) {
                    $line = $trace['line'] + 1;
                    break;
                }
            }

            $response->getBody()->write(\ob_get_clean() ?: '');
            $response = $response->withHeader('X-Error', '"' . $msg . '"; line=' . $line)->withStatus(500);
        }

        return $response;
    }
}
