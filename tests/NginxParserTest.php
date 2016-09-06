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
use WebHelper\Parser\NginxParser;

class NginxParserTest extends PHPUnit_Framework_TestCase
{
    private $parser;

    protected function setUp()
    {
        $this->parser = new NginxParser();
    }

    public function dataNginxParser()
    {
        $data = [];

        $data['test'] = [
            [
                'main' => [
                    ['events' => ['events {', '}']],
                    ['http' => [
                        'http {',
                        ['server' => [
                            'server {',
                            ['location /' => [
                                'location / {',
                                'root html;',
                                '}',
                            ]],
                            '}',
                        ]],
                        '}',
                    ]],
                ],
            ],
            __DIR__.'/data/nginx/test.conf',
        ];

        return $data;
    }

    /**
     * @dataProvider dataNginxParser
     */
    public function testNginxParser($expected, $configFile)
    {
        $getActiveConfig = $this->parser->setConfigFile($configFile)->getActiveConfig();
        $this->assertEquals($expected, $getActiveConfig);
    }

    /**
     * @expectedException WebHelper\Parser\InvalidConfigException
     */
    public function testNginxException()
    {
        $this->parser->setConfigFile(__DIR__.'/data/nginx/wrong-syntax.conf')->getActiveConfig();
    }
}
