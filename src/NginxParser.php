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
use WebHelper\Parser\Compiler;

/**
 * Nginx specific parser.
 *
 * @author James <james@rezo.net>
 */
class NginxParser extends BaseParser implements ParserInterface
{
    const SIMPLE_DIRECTIVE = '/^(?<key>\w+)(?<value>[^;]+);$/';
    const START_MULTI_LINE = '/^(?<key>[^\{]+)\{$/';
    const END_MULTI_LINE = '/^\}$/';

    /**
     * Getter for the active config array.
     *
     * @return array active config
     */
    public function getActiveConfig()
    {
        $this->compiler = new Compiler(self::START_MULTI_LINE, self::END_MULTI_LINE);
        return $this->compiler->doCompile($this->activeConfig);
    }

    /**
     * Does some extra parsing before the active configs turns into an array.
     *
     * @param string $config a config file content
     *
     * @return string a config file content
     */
    protected function beforeExplode($config)
    {
        $config = parent::beforeExplode($config);

        $config = preg_replace('/\{\n?/m', "{\n", $config);
        $config = preg_replace('/\n?\}/m', "\n}", $config);

        return $config;
    }
}
