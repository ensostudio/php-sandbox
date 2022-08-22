<?php

namespace EnsoStudio\PhpSandbox\Actions;

use Slim\Exception\HttpInternalServerErrorException;
use Slim\Psr7\Request;
use Slim\Psr7\Response;
use Throwable;

/**
 * Application action display sandbox HTML.
 */
class IndexAction implements ActionInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(Request $request, Response $response): Response
    {
        $response->getBody()->write(file_get_contents(__DIR__ . '/../view.php'));

        return $response;
    }
}
