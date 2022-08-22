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
        $tmpFile = \tmpfile();
        $uri = \stream_get_meta_data($tmpFile)['uri'];
        \fwrite($tmpFile, $request->getParsedBody()['code']);

        \ob_start();
        try {
            require __DIR__ . '/../bootstrap.php';
            include $uri;
            //eval('d([1]);');
            $response->getBody()->write(\ob_get_clean() . '#php-sandbox-end-output#');
        } catch (Throwable $e) {
            $response->getBody()->write(\ob_get_clean() ?: '');
            $msg = \get_class($e) . ': ' . \str_replace(['"', "\r\n", "\n"], ["'", "\n", ' '], $e->getMessage());
            $response = $response->withHeader('X-Error', '"' . $msg . '"; line=' . $e->getLine())->withStatus(500);
        }
        fclose($tmpFile);

        return $response;
    }
}
