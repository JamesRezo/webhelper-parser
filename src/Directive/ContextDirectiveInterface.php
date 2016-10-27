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
interface ContextDirectiveInterface extends DirectiveInterface
{
}
