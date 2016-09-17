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

class InclusionDirectiveTest extends PHPUnit_Framework_TestCase
{
    public function dataInclusionDirective()
    {
        $prefix = realpath(__DIR__.'/../data');

        return [
            'relative value with a prefix' => [
                [$prefix.'/empty1.conf'],
                'include',
                'empty1.conf',
                $prefix,
            ],
            'absolute value' => [
                [realpath(__DIR__.'/../data/empty1.conf')],
                'include',
                realpath(__DIR__.'/../data/empty1.conf'),
                '/tmp',
            ],
            'with a glob' => [
                [
                    realpath(__DIR__.'/../data/dos.conf'),
                    realpath(__DIR__.'/../data/dummy.conf'),
                    realpath(__DIR__.'/../data/empty1.conf'),
                    realpath(__DIR__.'/../data/empty2.conf'),
                ],
                'include',
                $prefix.'/*.conf',
                '/tmp',
            ],
        ];
    }

    /**
     * @dataProvider dataInclusionDirective
     */
    public function testInclusionDirective($expected, $name, $value, $prefix)
    {
        $directive = new InclusionDirective($name, $value, $prefix);

        $this->assertEquals($expected, $directive->getFiles());
    }
}
