<?php

/**
 * This file is part of WebHelper Parser.
 *
 * (c) James <james@rezo.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WebHelper\Test\Parser\Directive;

use PHPUnit_Framework_TestCase;
use WebHelper\Parser\Directive\SimpleDirective;

class DirectiveTest extends PHPUnit_Framework_TestCase
{
    private $directive;

    protected function setUp()
    {
        $this->directive = new SimpleDirective('name');
    }

    public function testDirective()
    {
        $directive = new SimpleDirective('name');
        $directive->add($this->directive);

        $this->assertEquals('name', $this->directive->getName());
        $this->assertEquals('', $this->directive->getValue());
        $this->assertEquals($directive, $this->directive);
    }
}
