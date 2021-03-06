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
 * Describes a directive instance.
 *
 * a Directive may be a simple key/value information or an ordered list
 * of simple directives set in a context (or block) with a name and an optional value
 * that can be itself nested in another block directive.
 *
 * @author James <james@rezo.net>
 */
interface DirectiveInterface
{
    /**
     * Gets the key name.
     *
     * @return string the key name
     */
    public function getName();

    /**
     * Get the key value.
     *
     * @return string the key value
     */
    public function getValue();

    /**
     * Adds a Directive at the end of a list.
     *
     * @param DirectiveInterface $directive a directive to add
     */
    public function add(DirectiveInterface $directive);

    /**
     * Confirms if the directive contains a specified directive.
     *
     * @param string $name the directive for which to check existence
     *
     * @return bool true if the sub-directive exists, false otherwise
     */
    public function hasDirective($name);

    /**
     * Confirms if the directive is simple.
     *
     * Simple directive cannot have sub directive
     *
     * @return bool true if the directive is simple, false otherwise
     */
    public function isSimple();

    /**
     * Dumps recursively the active configuration as a file.
     *
     * @param DirectiveInterface $activeConfig the active configuration to dump
     * @param int                $spaces       the indentation spaces
     *
     * @return string the file output
     */

    /**
     * Dumps the directive respecting a server syntax.
     *
     * @param ServerInterface $server a server instance
     * @param int             $spaces the indentation spaces
     *
     * @return string the dumped directive
     */
    public function dump(ServerInterface $server, $spaces = 0);
}
