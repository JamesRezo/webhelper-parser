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

use WebHelper\Parser\Parser as BaseParser;

/**
 * Apache specific parser.
 *
 * @author James <james@rezo.net>
 */
class ApacheParser extends BaseParser implements ParserInterface
{
    const START_MULTI_LINE = '/^<(?<key>\w+)(?<value>[^>]*)>$/';

    /**
     * Getter for the active config array.
     *
     * @return array active config
     */
    public function getActiveConfig()
    {
        return $this->compile($this->activeConfig);
    }

    /**
     * Does a nested array of lines depending on container Directives.
     *
     * @param  array $activeConfig a clean config array of lines
     *
     * @return array               a nested array of lines
     */
    private function compile($activeConfig)
    {
        $tempConfig = [];

        while (!empty($activeConfig)) {
            $lineConfig = array_shift($activeConfig);
            $tempConfig[] = $this->subCompile($activeConfig, $lineConfig);
        }

        return $tempConfig;
    }

    /**
     * Looks for a container directive.
     *
     * @param  array  $activeConfig a clean config array of lines
     * @param  string $lineConfig   a line
     *
     * @return mixed                a line or an array of line
     */
    private function subCompile(&$activeConfig, $lineConfig)
    {
        if (preg_match(self::START_MULTI_LINE, $lineConfig, $container)) {
            return $this->findEndingKey($container['key'], $activeConfig, $lineConfig);
        }

        return $lineConfig;
    }

    /**
     * Finds the end of a container directive.
     *
     * @param  string $key          a container's name
     * @param  array  $activeConfig a clean config array of lines
     * @param  string $lineConfig   a line
     *
     * @return array                a container of directives
     *
     * @throws ParserException      if a container does not end correctly
     */
    private function findEndingKey($key, &$activeConfig, $lineConfig)
    {
        $lines = [$lineConfig];

        while (!empty($activeConfig)) {
            $lineConfig = array_shift($activeConfig);
            $lines[] = $this->subCompile($activeConfig, $lineConfig);

            if (preg_match('/^<\/'.$key.'/', $lineConfig)) {
                return $lines;
            }
        }

        throw ParserException::forEndingKeyNotFound($key);
    }
}
