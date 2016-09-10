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
 * a BlockDirective is an ordered list of other directives set in a context.
 *
 * @author James <james@rezo.net>
 */
class BlockDirective extends Directive implements DirectiveInterface
{
    private $directives = [];

    /**
     * Adds a Directive at the end of the list.
     *
     * @param DirectiveInterface $directive a directive to add
     */
    public function add(DirectiveInterface $directive)
    {
        $this->directives[] = $directive;

        return $this;
    }

    /**
     * Confirms if the directive contains a specified directive.
     *
     * @param string $name the directive for which to check existence
     *
     * @return bool true if the sub-directive exists, false otherwise
     */
    public function hasDirective($name)
    {
        $inSubDirective = false;

        foreach ($this->directives as $index => $directive) {
            if ($directive->getName() == $name) {
                return true;
            }

            if (!$directive->isSimple()) {
                $inSubDirective = $inSubDirective || $directive->hasDirective($name);
            }
        }

        return $inSubDirective;
    }

    public function isSimple()
    {
        return false;
    }
}
