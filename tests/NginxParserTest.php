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
use WebHelper\Parser\Server\Server;

class NginxParserTest extends PHPUnit_Framework_TestCase
{
    private $parser;

    protected function setUp()
    {
        $server = new Server();
        $server->setPrefix(realpath(__DIR__.'/data'));
        $this->parser = new NginxParser();
        $this->parser->setServer($server);
    }

    public function dataNginxParser()
    {
        $data = [];

        $main = new BlockDirective('main');
        $events = new BlockDirective('events');
        $http = new BlockDirective('http');
        $server = new BlockDirective('server');
        $location = new BlockDirective('location', '/');
        $root = new SimpleDirective('root', 'html');
        $main->add($events)->add($http->add($server->add($location->add($root))));
        $data['test'] = [
            $main,
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
     * @expectedException WebHelper\Parser\Exception\InvalidConfigException
     */
    public function testNginxException()
    {
        $this->parser->setConfigFile(__DIR__.'/data/nginx/wrong-syntax.conf')->getActiveConfig();
    }
}
