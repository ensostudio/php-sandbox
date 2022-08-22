<?php

namespace EnsoStudio\PhpSandbox\Actions;

use ArrayIterator;
use SplFileInfo;
use Slim\Exception\HttpInternalServerErrorException;
use Slim\Psr7\Request;
use Slim\Psr7\Response;
use PhpCsFixer\Config;
use PhpCsFixer\Console\ConfigurationResolver;
use PhpCsFixer\ToolInfo;
use PhpCsFixer\Runner\Runner;
use PhpCsFixer\Error\ErrorsManager;
use Throwable;

/**
 * Application action formats the code using [PHP CS Fixer](https://github.com/FriendsOfPHP/PHP-CS-Fixer).
 */
class FormatAction implements ActionInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(Request $request, Response $response): Response
    {
        try {
            $code = $this->formatCodeStyle($request->getParsedBody()['code']);
            $response->getBody()->write($code);
        } catch (Throwable $e) {
            throw new HttpInternalServerErrorException(
                $request,
                'The code style formatting error: ' . $e->getMessage(),
                $e
            );
        }

        return $response;
    }

    /**
     * Returns the reformatted code.
     *
     * @param string $code the PHP code to format
     */
    private function formatCodeStyle(string $code): string
    {
        // Save code into templorary file
        $tempFile = \sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'php-sandbox.php';
        \file_put_contents($tempFile, $code);
        unset($code);

        /**
         * @see https://mlocati.github.io/php-cs-fixer-configurator/#version:3.8.0|configuration Configurator
         */
        $config = (new Config())
            ->setRules([
                '@PSR12' => true,
                // PHP arrays should be declared using the configured syntax
                'array_syntax' => ['syntax' => 'short'],
                // Binary operators should be surrounded by space as configured
                'binary_operator_spaces' => ['default' => 'single_space'],
                // A single space or none should be between cast and variable
                'cast_spaces' => ['space' => 'single'],
                // When referencing an internal class it must be written using the correct casing
                'class_reference_name_casing' => true,
                // Concatenation should be spaced according configuration
                'concat_space' => ['spacing' => 'one'],
                // Ensure single space between function's argument and it's type hint
                'function_typehint_space' => true,
                // Single-line whitespace before closing semicolon are prohibited
                'no_singleline_whitespace_before_semicolons' => true,
                // There MUST NOT be spaces around offset braces
                'no_spaces_around_offset' => true,
                // Operator `=>` should not be surrounded by multi-line whitespaces
                'no_multiline_whitespace_around_double_arrow' => true,
                // Arrays should be formatted like function/method arguments, without leading or trailing single line space
                'trim_array_spaces' => true,
                // Unary operators should be placed adjacent to their operands
                'unary_operator_spaces' => true,
                // In array declaration, there MUST be a whitespace after each comma
                'whitespace_after_comma_in_array' => true,
            ])
            ->setUsingCache(false)
            ->setHideProgress(true)
            ->setRiskyAllowed(true)
            ->setIndent('    ')
            ->setLineEnding("\n")
            
            ->setFinder(new ArrayIterator([new SplFileInfo($tempFile)]));

        $resolver = new ConfigurationResolver(
            $config,
            ['dry-run' => false, 'stop-on-violation' => false],
            \getcwd(),
            new ToolInfo()
        );
        $runner = new Runner(
            $config->getFinder(),
            $resolver->getFixers(),
            $resolver->getDiffer(),
            null,
            new ErrorsManager(),
            $resolver->getLinter(),
            $resolver->isDryRun(),
            $resolver->getCacheManager(),
            $resolver->getDirectory(),
            $resolver->shouldStopOnViolation()
        );
        $runner->fix();

        $code = \file_get_contents($tempFile);
        unlink($tempFile);

        return $code;
    }
}
