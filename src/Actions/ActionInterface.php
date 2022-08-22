<?php

namespace EnsoStudio\PhpSandbox\Actions;

use Slim\Psr7\Request;
use Slim\Psr7\Response;

/**
 * The application action.
 */
interface ActionInterface
{
    /**
     * Executes action and returns updated response.
     */
    public function __invoke(Request $request, Response $response): Response;
}
