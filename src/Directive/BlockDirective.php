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

use IteratorAggregate;
use ArrayIterator;

/**
 * Describes a block directive instance or a context.
 *
 * a BlockDirective is an ordered list of other directives set in a context.
 *
 * @author James <james@rezo.net>
 */
class BlockDirective extends Directive implements DirectiveInterface, IteratorAggregate
{
    /** @var array orderd list of sub directives */
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

        foreach ($this->directives as $directive) {
            if ($directive->getName() == $name) {
                return true;
            }

            $inSubDirective = $this->hasInnerDirective($name, $inSubDirective, $directive);
        }

        return $inSubDirective;
    }

    /**
     * Looks into sub directives to confirm if the actual contains a specified directive.
     *
     * @param string             $name           the directive for which to check existence
     * @param bool               $inSubDirective the actual state
     * @param DirectiveInterface $directive      the sub directive to look into
     *
     * @return bool true if the sub-directive exists, false otherwise
     */
    private function hasInnerDirective($name, $inSubDirective, DirectiveInterface $directive)
    {
        if (!$directive->isSimple()) {
            $inSubDirective = $inSubDirective || $directive->hasDirective($name);
        }

        return $inSubDirective;
    }

    /**
     * Confirms if the directive is simple.
     *
     * Block directive can have sub directives
     *
     * @return bool true if the directive is simple, false otherwise
     */
    public function isSimple()
    {
        return false;
    }

    /**
     * Gets the sub directives as a Traversable array.
     *
     * @return ArrayIterator the sub directives
     */
    public function getIterator()
    {
        return new ArrayIterator($this->directives);
    }
}
