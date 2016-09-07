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
    const SIMPLE_DIRECTIVE = '/^(?<key>\w+)(?<value>.+)$/';
    const START_MULTI_LINE = '/^<(?<key>\w+)(?<value>[^>]*)>$/';
    const END_MULTI_LINE = '/^<\/%s>/';

    /**
     * Getter for the active config array.
     *
     * @return array active config
     */
    public function getActiveConfig()
    {
        $this->compiler = new Compiler(self::START_MULTI_LINE, self::END_MULTI_LINE, self::SIMPLE_DIRECTIVE);

        return $this->compiler->doCompile($this->activeConfig);
    }
}
