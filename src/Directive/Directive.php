<?php

/**
 * This file is part of WebHelper Parser.
 *
 * (c) James <james@rezo.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WebHelper\Parser\Directive;

/**
 * This is a simple directive implementation that other Directives can inherit from.
 *
 * a Directive may be a simple key/value information or an ordered list
 * of simple directives set in a context (or block) with a name and an optional value
 * that can be itself nested in another block directive.
 *
 * @author James <james@rezo.net>
 */
abstract class Directive implements DirectiveInterface
{
    private $name;

    private $value;

    public function __construct($name, $value = '')
    {
        $this->name = $name;
        $this->value = $value;
    }

    /**
     * Gets the key name.
     *
     * @return string the key name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get the key value.
     *
     * @return string the key value
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Adds a Directive at the end of a list.
     *
     * @param DirectiveInterface $directive a directive to add
     */
    public function add(DirectiveInterface $directive)
    {
        return $this;
    }

    /**
     * Confirms if the directive contains a specified directive.
     *
     * @param string $name the directive for which to check existence
     *
     * @return bool true if the sub-directive exists, false otherwise
     */
    abstract public function hasDirective($name);
}
