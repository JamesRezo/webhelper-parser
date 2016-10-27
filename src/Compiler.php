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

use WebHelper\Parser\Directive\SimpleDirective;
use WebHelper\Parser\Exception\InvalidConfigException;

/**
 * Web server configuration generic compiler.
 *
 * @author James <james@rezo.net>
 */
class Compiler
{
    /**
     * The parser instance.
     *
     * @var Parser
     */
    private $parser;

    /**
     * The string to match as a starting multi-line directive.
     *
     * @var string
     */
    private $startMultiLine;

    /**
     * The string to match as an ending multi-line directive.
     *
     * @var string
     */
    private $endMultiLine;

    /**
     * The string to match a simple Directive.
     *
     * @var string
     */
    private $simpleDirective;

    /**
     * Constructor.
     *
     * @param string $startMultiLine     match as a starting multi-line directive
     * @param string $endMultiLine       match as an ending multi-line directive
     * @param string $simpleDirective    match a simple directive
     */
    public function __construct($startMultiLine, $endMultiLine, $simpleDirective)
    {
        $this->startMultiLine = $startMultiLine;
        $this->endMultiLine = $endMultiLine;
        $this->simpleDirective = $simpleDirective;
    }

    /**
     * Sets the parser instance.
     *
     * @param Parser $parser the parser instance
     */
    public function setParser(Parser $parser)
    {
        $this->parser = $parser;

        return $this;
    }

    /**
     * Does a nested array of lines depending on container Directives.
     *
     * @param array  $activeConfig a clean config array of lines
     * @param string $context      the context name
     * @param string $value        an optional context value
     *
     * @return Directive\BlockDirective a full context of directives
     */
    public function doCompile($activeConfig, $context = 'main', $value = '')
    {
        $tempConfig = [];

        while (!empty($activeConfig)) {
            $lineConfig = array_shift($activeConfig);
            $tempConfig[] = $this->subCompile($activeConfig, $lineConfig);
        }

        return $this->buildBlockDirective($context, $value, $tempConfig);
    }

    /**
     * Looks for a container directive.
     *
     * @param array  $activeConfig a clean config array of directives
     * @param string $lineConfig   a line
     *
     * @throws Exception\InvalidConfigException if a simple directive has invalid syntax
     *
     * @return Directive\DirectiveInterface a directive or a container of directives
     */
    private function subCompile(&$activeConfig, $lineConfig)
    {
        if (preg_match($this->startMultiLine, $lineConfig, $container)) {
            return $this->findEndingKey(trim($container['key']), trim($container['value']), $activeConfig);
        }

        if (!preg_match($this->simpleDirective, $lineConfig, $container)) {
            throw InvalidConfigException::forSimpleDirectiveSyntaxError($lineConfig);
        }

        return $this->buildSimpleDirective(trim($container['key']), trim($container['value']));
    }

    /**
     * Finds the end of a container directive.
     *
     * @param string $context      a container's name
     * @param string $contextValue a container's value
     * @param array  $activeConfig a clean config array of lines
     *
     * @throws Exception\InvalidConfigException if a container does not end correctly
     *
     * @return Directive\BlockDirective a container of directives
     */
    private function findEndingKey($context, $contextValue, &$activeConfig)
    {
        $lines = [];
        $endMultiLine = sprintf($this->endMultiLine, $context);

        while (!empty($activeConfig)) {
            $lineConfig = array_shift($activeConfig);

            if (preg_match($endMultiLine, $lineConfig)) {
                return $this->buildBlockDirective($context, $contextValue, $lines);
            }

            $lines[] = $this->subCompile($activeConfig, $lineConfig);
        }

        throw InvalidConfigException::forEndingKeyNotFound($context);
    }

    /**
     * Builds a BlockDirective.
     *
     * @param string $context      a container's name
     * @param string $contextValue a container's value
     * @param array  $lines        an array of directives
     *
     * @return Directive\BlockDirective the BlockDirective
     */
    private function buildBlockDirective($context, $contextValue, $lines)
    {
        $class = 'WebHelper\Parser\Directive\BlockDirective';
        $known = $this->parser->getServer()->getKnownDirectives();
        if (in_array($context, array_keys($known))) {
            if (isset($known[$key]['class'])) {
                $class = 'WebHelper\Parser\Directive\\'.$known[$context]['class'];
            }
        }
        $block = new $class($context, $contextValue);
        foreach ($lines as $directive) {
            $block->add($directive);
        }

        return $block;
    }

    /**
     * Build a SimpleDirective or an InclusionDirective.
     *
     * Remember that inclusion are parsed as simple directives but are block directives
     *
     * @see Directive\InclusionDirective Inclusion Doc
     *
     * @param string $key   a directive's name
     * @param string $value a directive's value
     *
     * @return Directive\SimpleDirective|Directive\InclusionDirective the Directive
     */
    private function buildSimpleDirective($key, $value)
    {
        $known = $this->parser->getServer()->getKnownDirectives();
        if (in_array($key, array_keys($known))) {
            if (isset($known[$key]['class'])) {
                $class = 'WebHelper\Parser\Directive\\'.$known[$key]['class'];
                return new $class($key, $value, $this->parser);
            }
        }

        return new SimpleDirective($key, $value);
    }
}
