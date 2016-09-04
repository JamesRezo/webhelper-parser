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
     * [compile description]
     *
     * @param  [type] $activeConfig [description]
     *
     * @return [type]               [description]
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
     * [subCompile description]
     *
     * @param  [type] &$activeConfig [description]
     * @param  [type] $lineConfig    [description]
     *
     * @return [type]                [description]
     */
    private function subCompile(&$activeConfig, $lineConfig)
    {
        if (preg_match(self::START_MULTI_LINE, $lineConfig, $container)) {
            return $this->findEndingKey($container['key'], $activeConfig, $lineConfig);
        }

        return $lineConfig;
    }

    /**
     * [findEndingKey description]
     *
     * @param  [type] $key           [description]
     * @param  [type] &$activeConfig [description]
     * @param  [type] $lineConfig    [description]
     *
     * @return [type]                [description]
     *
     * @throws ParserException       if a container does not end correctly
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
