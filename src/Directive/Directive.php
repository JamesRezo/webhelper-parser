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

use WebHelper\Parser\Server\ServerInterface;

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
    /** @var string the name of the key/context */
    private $name;

    /** @var string the value of the key/context */
    private $value;

    /**
     * Base contructor.
     *
     * @param string $name  the name of the key/context to instanciate
     * @param string $value the optional value of the key/context to instanciate
     */
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

    /**
     * Confirms if the directive is simple.
     *
     * Simple directive cannot have sub directive
     *
     * @return bool true if the directive is simple, false otherwise
     */
    abstract public function isSimple();

    /**
     * Dumps the directive respecting a server syntax.
     *
     * @param ServerInterface $server a server instance
     * @param int             $spaces the indentation spaces
     *
     * @return string the dumped directive
     */
    abstract public function dump(ServerInterface $server, $spaces = 0);

    /**
     * Dumps a simple directive.
     *
     * @param ServerInterface $server a server instance
     * @param int             $spaces the indentation spaces
     *
     * @return string the dumped simple directive
     */
    protected function dumpSimple(ServerInterface $server, $spaces = 0)
    {
        $value = $this->getValue() ? ' '.$this->getValue() : '';

        return str_repeat(' ', $spaces).sprintf(
            $server->getDumperSimpleDirective(),
            $this->getName(),
            $value
        ).PHP_EOL;
    }
}
