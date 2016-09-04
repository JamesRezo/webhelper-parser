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
 * Nginx specific parser.
 *
 * @author James <james@rezo.net>
 */
class NginxParser extends BaseParser implements ParserInterface
{
    const SIMPLE_DIRECTIVE = '/^(?<key>\w+)(?<value>[^;]+);$/';
    const START_MULTI_LINE = '/^(?<key>[^\{]+)\{$/';

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
     * [beforeExplode description].
     *
     * @param string $config [description]
     *
     * @return string [description]
     */
    protected function beforeExplode($config)
    {
        $config = preg_replace('/\{\n?/m', "{\n", $config);
        $config = preg_replace('/\n?\}/m', "\n}", $config);

        return $config;
    }

    /**
     * Does a nested array of lines depending on container Directives.
     *
     * @param array  $activeConfig a clean config array of lines
     * @param string $context      the context name
     *
     * @return array a nested array of lines
     */
    private function compile($activeConfig, $context = 'main')
    {
        $tempConfig = [];

        while (!empty($activeConfig)) {
            $lineConfig = array_shift($activeConfig);
            $tempConfig[] = $this->subCompile($activeConfig, $lineConfig);
        }

        return [$context => $tempConfig];
    }

    private function subCompile(&$activeConfig, $lineConfig)
    {
        if (preg_match(self::START_MULTI_LINE, $lineConfig, $container)) {
            return $this->findEndingKey(trim($container['key']), $activeConfig, $lineConfig);
        }

        return $lineConfig;
    }

    private function findEndingKey($context, &$activeConfig, $lineConfig)
    {
        $lines = [$lineConfig];

        while (!empty($activeConfig)) {
            $lineConfig = array_shift($activeConfig);
            $lines[] = $this->subCompile($activeConfig, $lineConfig);

            if (preg_match('/^\}$/', $lineConfig)) {
                return [$context => $lines];
            }
        }

        throw ParserException::forEndingKeyNotFound($context);
    }
}
