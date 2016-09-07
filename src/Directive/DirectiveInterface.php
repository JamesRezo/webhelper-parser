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
}
