<?php

/**
 * This file is part of WebHelper Parser.
 *
 * (c) James <james@rezo.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WebHelper\Parser;

/**
 * Web server configuration generic compiler.
 *
 * @author James <james@rezo.net>
 */
class Compiler
{
    private $startMultiLine;
    private $endMultiLine;

    public function __construct($startMultiLine, $endMultiLine)
    {
        $this->startMultiLine = $startMultiLine;
        $this->endMultiLine = $endMultiLine;
    }

    /**
     * Does a nested array of lines depending on container Directives.
     *
     * @param array  $activeConfig a clean config array of lines
     * @param string $context      the context name
     *
     * @return array a nested array of lines
     */
    public function doCompile($activeConfig, $context = 'main')
    {
        $tempConfig = [];

        while (!empty($activeConfig)) {
            $lineConfig = array_shift($activeConfig);
            $tempConfig[] = $this->subCompile($activeConfig, $lineConfig);
        }

        return [$context => $tempConfig];
    }

    /**
     * Looks for a container directive.
     *
     * @param array  $activeConfig a clean config array of lines
     * @param string $lineConfig   a line
     *
     * @return mixed a line or an array of line
     */
    private function subCompile(&$activeConfig, $lineConfig)
    {
        if (preg_match($this->startMultiLine, $lineConfig, $container)) {
            return $this->findEndingKey(trim($container['key']), $activeConfig, $lineConfig);
        }

        return $lineConfig;
    }

    /**
     * Finds the end of a container directive.
     *
     * @param string $context          a container's name
     * @param array  $activeConfig a clean config array of lines
     * @param string $lineConfig   the starting config line of the container
     *
     * @return array a container of directives
     *
     * @throws InvalidConfigException if a container does not end correctly
     */
    private function findEndingKey($context, &$activeConfig, $lineConfig)
    {
        $lines = [$lineConfig];
        $endMultiLine = sprintf($this->endMultiLine, $context);

        while (!empty($activeConfig)) {
            $lineConfig = array_shift($activeConfig);
            $lines[] = $this->subCompile($activeConfig, $lineConfig);

            if (preg_match($endMultiLine, $lineConfig)) {
                return [$context => $lines];
            }
        }

        throw InvalidConfigException::forEndingKeyNotFound($context);
    }
}
