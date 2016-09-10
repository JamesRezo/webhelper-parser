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

    /** @var Compiler a Compiler instance */
    private $compiler;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->compiler = new Compiler(self::START_MULTI_LINE, self::END_MULTI_LINE, self::SIMPLE_DIRECTIVE);
    }

    /**
     * Getter for the active config main context.
     *
     * @return Directive\DirectiveInterface the active config
     */
    public function getActiveConfig()
    {
        return $this->compiler->doCompile($this->activeConfig);
    }

    /**
     * Does some extra parsing after the active config has turned into an array.
     *
     * @param array $activeConfig an active config
     *
     * @return array an active config
     */
    protected function afterExplode(array $activeConfig)
    {
        $activeConfig = parent::afterExplode($activeConfig);
        $cleanedActiveConfig = [];

        //Continuing directives with "\" at the very end of a line are reassembled
        $previousLine = '';
        foreach ($activeConfig as $line) {
            if ($previousLine) {
                $line = $previousLine.' '.$line;
                $previousLine = '';
            }

            if (preg_match('/(.+)\\\$/', $line, $container)) {
                $previousLine = $container[1];
            }

            if (!$previousLine) {
                $cleanedActiveConfig[] = $line;
            }
        }

        return $cleanedActiveConfig;
    }
}
