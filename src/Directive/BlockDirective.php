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
 * Describes a block directive instance or a context.
 *
 * a BlockDirective is an ordered list of other directives set in a context.
 *
 * @author James <james@rezo.net>
 */
class BlockDirective extends Directive implements DirectiveInterface
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
     * Dumps the directive respecting a server syntax.
     *
     * @param ServerInterface $server a server instance
     * @param int             $spaces the indentation spaces
     *
     * @return string the dumped directive
     */
    public function dump(ServerInterface $server, $spaces = 0)
    {
        $config = '';

        if (!$this->isMainContext()) {
            $value = $this->getValue() ? ' '.$this->getValue() : '';
            $config .= str_repeat(' ', $spaces).sprintf(
                $server->getDumperStartDirective(),
                $this->getName(),
                $value
            ).PHP_EOL;
        }

        foreach ($this->directives as $directive) {
            $config .= $directive->dump($server, $spaces + ($this->isMainContext() ? 0 : 4));
        }

        if (!$this->isMainContext()) {
            $config .= str_repeat(' ', $spaces).sprintf(
                $server->getDumperEndDirective(),
                $this->getName(),
                $value
            ).PHP_EOL;
        }

        return $config;
    }

    /**
     * Confirms if a Block directive is a 'main' context.
     *
     * @return bool true if the name of the block directive is 'main', false otherwise
     */
    private function isMainContext()
    {
        return 'main' == $this->getName();
    }
}
