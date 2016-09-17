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
use WebHelper\Parser\Compiler;

class InclusionDirectiveTest extends PHPUnit_Framework_TestCase
{
    public function dataInclusionDirective()
    {
        $compiler = new Compiler(
            '/^(?<key>\w+)(?<value>[^\{]+)\{$/',
            '/^\}$/',
            '/^(?<key>\w+)(?<value>[^;]+);$/',
            '/^include$/'
        );
        $compiler->setPrefix(realpath(__DIR__.'/../data'));

        return [
            'relative value with a prefix' => [
                [realpath(__DIR__.'/../data/empty1.conf')],
                'include',
                'empty1.conf',
                $compiler,
            ],
            'absolute value' => [
                [realpath(__DIR__.'/../data/empty1.conf')],
                'include',
                realpath(__DIR__.'/../data/empty1.conf'),
                $compiler,
            ],
            'with a glob' => [
                [
                    realpath(__DIR__.'/../data/dos.conf'),
                    realpath(__DIR__.'/../data/dummy.conf'),
                    realpath(__DIR__.'/../data/empty1.conf'),
                    realpath(__DIR__.'/../data/empty2.conf'),
                ],
                'include',
                realpath(__DIR__.'/../data').'/*.conf',
                $compiler,
            ],
        ];
    }

    /**
     * @dataProvider dataInclusionDirective
     */
    public function testInclusionDirective($expected, $name, $value, $compiler)
    {
        $directive = new InclusionDirective($name, $value, $compiler);

        $this->assertEquals($expected, $directive->getFiles());
    }
}
