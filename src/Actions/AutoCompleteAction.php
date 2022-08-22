<?php

namespace EnsoStudio\PhpSandbox\Actions;

use Slim\Exception\HttpInternalServerErrorException;
use Slim\Psr7\Request;
use Slim\Psr7\Response;
use JsonException;

/**
 * Application action: Auto-complete names of PHP classes, interfaces and constants.
 */
class AutoCompleteAction implements ActionInterface
{
    public function __invoke(Request $request, Response $response): Response
    {
        $result = $this->find($request->getQueryParams()['prefix']);
        try {
            $result = \json_encode($result, \JSON_UNESCAPED_UNICODE | \JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw new HttpInternalServerErrorException($request, 'JSON encoding error', $e);
        }
        $response->getBody()->write($result);

        return $response->withHeader('Content-Type', 'application/json; charset=UTF-8');
    }

    /**
     * Finds the PHP classes, interfaces and/or constants by given prefix.
     *
     * @param string $prefix the prefix to search
     * @return array[]
     */
    private function find(string $prefix): array
    {
        $list = [];
        // If prefix in upper case, then in most cases, is constant.
        if ($prefix === \strtoupper($prefix)) {
            $list['constants'] = \get_defined_constants();
        }
        $list['classes'] = \get_declared_classes();
        if ('A' >= $prefix[0] && $prefix[0] <= 'Z') {
            $list['interfaces'] = \get_declared_interfaces();
        }

        $prefixLength = \strlen($prefix);
        $result = (array) \array_filter(
            \array_merge(...\array_values($list)),
            static function ($value) use ($prefix, $prefixLength) {
                return \strncmp($value, $prefix, $prefixLength) === 0;
            }
        );

        if (!empty($result)) {
            \natsort($result);

            foreach ($result as $key => $value) {
                foreach ($list as $group => $values) {
                    if (\in_array($value, $values, true)) {
                        break;
                    }
                }
                // https://github.com/ajaxorg/ace/blob/v1.9.6/src/autocomplete/text_completer.js#L39
                $result[$key] = [
                    'caption' => $value,
                    'value' => $value,
                    'score' => 1000000,
                    'meta' => $group
                ];
            }

            $result = \array_values($result);
        }

        return $result;
    }
}
