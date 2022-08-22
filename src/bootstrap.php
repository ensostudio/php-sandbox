<?php

/**
 * @see https://kint-php.github.io/kint/settings/ Kint configuration
 */
Kint\Kint::$display_called_from = false;
Kint\Kint::$return = false;
Kint\Kint::$cli_detection = false;
Kint\Kint::$depth_limit = 10;
Kint\Kint::$file_link_format = 'phpstorm://open?url=file://%f&line=%l';
Kint\Parser\ArrayLimitPlugin::$trigger = 500;
Kint\Parser\ArrayLimitPlugin::$limit = 250;
Kint\Renderer\RichRenderer::$strlen_max = 1024;

/**
 * Reflection helpers.
 */

if (!function_exists('rc')) {
    /**
     * @param string|object $class
     * @return string
     * @throws ReflectionException
     */
    function rc($class)
    {
        // gets brief description of given class/interface
        $result = preg_replace(
            ['/\s+?- [\w\h]+ \[0\] \{\s+?\}/', '/@@ [^\s]+ \d+\s?-\s?\d+\s*/'],
            '',
            (string) new ReflectionClass($class)
        );
        $result = htmlspecialchars($result, ENT_HTML5 | ENT_NOQUOTES);
        echo '<pre class="reflection fs-6 py-1">' . $result . '</pre>';
    }
}
if (!function_exists('rm')) {
    /**
     * @param string|object $classOrObject
     * @param string $method the method name
     * @return string
     */
    function rm($class, string $method)
    {
        // gets brief description of given method
        $str = preg_replace(
            ['/\s+?- [\w\h]+ \[0\] \{\s+?\}/', '/@@ [^\s]+ \d+\s?-\s?\d+\s*/'],
            '',
            (string) (new ReflectionClass($class))->getMethod($method)
        );
        $str = htmlspecialchars($str, ENT_HTML5 | ENT_NOQUOTES);
        echo '<pre class="reflection fs-6 py-1">' . $str . '</pre>';
    }
}
if (!function_exists('rf')) {
    /**
     * @param string $function the function name
     * @return string
     */
    function rf(string $function)
    {
        // gets brief description of given function
        $str = preg_replace(
            ['/\s+?- [\w\h]+ \[0\] \{\s+?\}/', '/@@ [^\s]+ \d+\s?-\s?\d+\s*/'],
            '',
            (string) new ReflectionFunction($function)
        );
        $str = htmlspecialchars($str, ENT_HTML5 | ENT_NOQUOTES);
        echo '<pre class="reflection fs-6 py-1">' . $str . '</pre>';
    }
}