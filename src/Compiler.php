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
use WebHelper\Parser\Directive\BlockDirective;
use WebHelper\Parser\Directive\InclusionDirective;
use WebHelper\Parser\Exception\InvalidConfigException;

/**
 * Web server configuration generic compiler.
 *
 * @author James <james@rezo.net>
 */
class Compiler
{
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
     * The string to match an inclusion Directive.
     *
     * @var string
     */
    private $inclusionDirective;

    /**
     * An absolute path prefix.
     *
     * The compiler needs an absolute path prefix to find included files set with a relative path
     *
     * @var string
     */
    private $prefix;

    /**
     * Constructor.
     *
     * @param string $startMultiLine  match as a starting multi-line directive
     * @param string $endMultiLine    match as an ending multi-line directive
     * @param string $simpleDirective match a simple directive
     * @param string $inclusionDirective match an inclusion directive
     */
    public function __construct($startMultiLine, $endMultiLine, $simpleDirective, $inclusionDirective)
    {
        $this->startMultiLine = $startMultiLine;
        $this->endMultiLine = $endMultiLine;
        $this->simpleDirective = $simpleDirective;
        $this->inclusionDirective = $inclusionDirective;
    }

    /**
     * Sets an absolute path prefix.
     *
     * @param string $prefix an absolute path prefix
     */
    public function setPrefix($prefix = '')
    {
        $this->prefix = $prefix;

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
     * @return Directive\DirectiveInterface a directive or a container of directives
     *
     * @throws Exception\InvalidConfigException if a simple directive has invalid syntax
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
     * @return Directive\BlockDirective a container of directives
     *
     * @throws Exception\InvalidConfigException if a container does not end correctly
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
        $block = new BlockDirective($context, $contextValue);
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
        if (preg_match($this->inclusionDirective, $key)) {
            return new InclusionDirective($key, $value, $this->prefix);
        }

        return new SimpleDirective($key, $value);
    }
}
