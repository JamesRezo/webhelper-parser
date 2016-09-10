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
 * Describes a simple directive instance.
 *
 * a SimpleDirective is a simple key/value information.
 *
 * @author James <james@rezo.net>
 */
class SimpleDirective extends Directive implements DirectiveInterface
{
    /**
     * Confirms if the directive contains a specified directive.
     *
     * @param string $name the directive for which to check existence
     *
     * @return bool true if the sub-directive exists, false otherwise
     */
    public function hasDirective($name)
    {
        return false;
    }

    public function isSimple()
    {
        return true;
    }
}
