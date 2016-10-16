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
use WebHelper\Parser\Directive\BlockDirective;
use WebHelper\Parser\Directive\SimpleDirective;

class DirectiveTest extends PHPUnit_Framework_TestCase
{
    private $directive;

    protected function setUp()
    {
        $this->directive = new SimpleDirective('name');
    }

    public function testSimpleDirective()
    {
        $otherdirective = new SimpleDirective('name');
        $this->directive->add($otherdirective);

        $this->assertEquals('name', $this->directive->getName());
        $this->assertEquals('', $this->directive->getValue());
        $this->assertEquals($otherdirective, $this->directive);
        $this->assertFalse($this->directive->hasDirective('name'));
    }

    public function testBlockDirective()
    {
        $directive = new BlockDirective('context');
        $subdirective = new BlockDirective('subcontext');
        $subdirective->add(new SimpleDirective('othername'));
        $directive->add($this->directive);
        $directive->add($subdirective);

        $this->assertTrue($directive->hasDirective('name'));
        $this->assertTrue($directive->hasDirective('othername'));
        $this->assertFalse($directive->hasDirective('doesnotexist'));
    }
}
