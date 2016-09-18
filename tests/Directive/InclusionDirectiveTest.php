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
use WebHelper\Parser\Directive\InclusionDirective;
use WebHelper\Parser\Factory;

class InclusionDirectiveTest extends PHPUnit_Framework_TestCase
{
    public function dataInclusionDirective()
    {
        $factory = new Factory();
        $parser = $factory->createParser('apache');
        $parser->getServer()->setPrefix(realpath(__DIR__.'/../data'));

        return [
            'relative value with a prefix' => [
                [realpath(__DIR__.'/../data/dummy.conf')],
                'include',
                'dummy.conf',
                $parser,
            ],
            'absolute value' => [
                [realpath(__DIR__.'/../data/dummy.conf')],
                'include',
                realpath(__DIR__.'/../data/dummy.conf'),
                $parser,
            ],
            'with a glob' => [
                [
                    realpath(__DIR__.'/../data/dos.conf'),
                    realpath(__DIR__.'/../data/dummy.conf'),
                ],
                'include',
                realpath(__DIR__.'/../data').'/*.conf',
                $parser,
            ],
        ];
    }

    /**
     * @dataProvider dataInclusionDirective
     */
    public function testInclusionDirective($expected, $name, $value, $parser)
    {
        $directive = new InclusionDirective($name, $value, $parser);

        $this->assertEquals($expected, $directive->getFiles());
    }
}
