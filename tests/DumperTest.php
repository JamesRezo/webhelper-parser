<?php

/**
 * This file is part of WebHelper Parser.
 *
 * (c) James <james@rezo.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WebHelper\Test\Parser;

use PHPUnit_Framework_TestCase;
use WebHelper\Parser\Dumper;
use WebHelper\Parser\Server\Server;
use WebHelper\Parser\Directive\SimpleDirective;
use WebHelper\Parser\Directive\BlockDirective;

class DumperTest extends PHPUnit_Framework_TestCase
{
    private $dumper;

    protected function setUp()
    {
        $server = new Server();
        $server->setDumperSimpleDirective('%s.%s;')->setDumperStartDirective('%s.%s (')->setDumperEndDirective(')');
        $this->dumper = new Dumper();
        $this->dumper->setServer($server);
    }

    public function dataIntegration()
    {
        $main = new BlockDirective('main');
        $block = new BlockDirective('BlockDirective', 'test');
        $block->add(new SimpleDirective('InnerDirective', 'other_value'));
        $main
            ->add(new SimpleDirective('Directive', 'dummy_value'))
            ->add($block)
            ->add(new SimpleDirective('AfterBlankLineDirective', 'some-text and spaces'))
        ;

        return [
            'nominal case' => [
                'Directive.dummy_value;
BlockDirective.test (
    InnerDirective.other_value;
)
AfterBlankLineDirective.some-text and spaces;
',
                $main,
            ],
        ];
    }

    /**
     * @dataProvider dataIntegration
     */
    public function testIntegration($expected, $activeConfig)
    {
        $this->assertEquals($expected, $this->dumper->dump($activeConfig));
    }
}
