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
use WebHelper\Parser\Directive\SimpleDirective;
use WebHelper\Parser\Directive\BlockDirective;

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

        $events = new BlockDirective('events');
        $http = new BlockDirective('http');
        $server = new BlockDirective('server');
        $location = new BlockDirective('location', '/');
        $root = new SimpleDirective('root', 'html');
        $http->add($server->add($location->add($root)));
        $data['test'] = [
            [
                'main' => [
                    $events,
                    $http,
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
