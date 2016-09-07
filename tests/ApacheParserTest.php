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
use WebHelper\Parser\ApacheParser;

class ApacheParserTest extends PHPUnit_Framework_TestCase
{
    private $parser;

    protected function setUp()
    {
        $this->parser = new ApacheParser();
    }

    public function dataApacheParser()
    {
        $data = [];

        $data['no multi line'] = [
            ['main' => [
                'ServerRoot "/usr"',
                'Listen 80',
                'ServerName localhost',
                'DocumentRoot "/var/www/php"',
            ]],
            __DIR__.'/data/apache/no-multi-line.conf',
        ];

        $data['one multi line'] = [
            ['main' => [
                'ServerRoot "/usr"',
                [
                    'Directory' => [
                        'value' => '/',
                        'block' => [
                            'AllowOverride none',
                            'Require all denied',
                        ],
                    ],
                ],
            ]],
            __DIR__.'/data/apache/one-multi-line.conf',
        ];

        $data['nested multi lines'] = [
            ['main' => [
                'ServerRoot "/usr"',
                [
                    'IfModule' => [
                        'value' => 'log_config_module',
                        'block' => [
                            'LogFormat "%h %l %u %t \"%r\" %>s %b \"%{Referer}i\" \"%{User-Agent}i\"" combined',
                            [
                                'IfModule' => [
                                    'value' => 'logio_module',
                                    'block' => [
                                        'LogFormat "%h %l %u %t \"%r\" %>s %b \"%{Referer}i\" \"%{User-Agent}i\" %I %O" combinedio',
                                    ],
                                ],
                            ],
                            'CustomLog "/var/log/apache2/access_log" common',
                        ],
                    ],
                ],
            ]],
            __DIR__.'/data/apache/nested-multi-lines.conf',
        ];

        return $data;
    }

    /**
     * @dataProvider dataApacheParser
     */
    public function testApacheParser($expected, $configFile)
    {
        $actual = $this->parser->setConfigFile($configFile)->getActiveConfig();
        $this->assertEquals($expected, $actual);
    }

    /**
     * @expectedException WebHelper\Parser\InvalidConfigException
     */
    public function testApacheException()
    {
        $this->parser->setConfigFile(__DIR__.'/data/apache/wrong-syntax.conf')->getActiveConfig();
    }
}
