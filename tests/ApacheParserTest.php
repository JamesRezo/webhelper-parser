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
use WebHelper\Parser\Directive\SimpleDirective;
use WebHelper\Parser\Directive\BlockDirective;

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
                new SimpleDirective('ServerRoot', '"/usr"'),
                new SimpleDirective('Listen', '80'),
                new SimpleDirective('ServerName', 'localhost'),
                new SimpleDirective('DocumentRoot', '"/var/www/php"'),
            ]],
            __DIR__.'/data/apache/no-multi-line.conf',
        ];

        $block = new BlockDirective('Directory', '/');
        $block
            ->add(new SimpleDirective('AllowOverride', 'none'))
            ->add(new SimpleDirective('Require', 'all denied'));
        $data['one multi line'] = [
            ['main' => [
                new SimpleDirective('ServerRoot', '"/usr"'),
                $block,
            ]],
            __DIR__.'/data/apache/one-multi-line.conf',
        ];

        $block = new BlockDirective('IfModule', 'log_config_module');
        $nestedBlock = new BlockDirective('IfModule', 'logio_module');
        $nestedBlock
            ->add(new SimpleDirective(
                'LogFormat',
                '"%h %l %u %t \"%r\" %>s %b \"%{Referer}i\" \"%{User-Agent}i\" %I %O" combinedio'
            ));
        $block
            ->add(new SimpleDirective(
                'LogFormat',
                '"%h %l %u %t \"%r\" %>s %b \"%{Referer}i\" \"%{User-Agent}i\"" combined'
            ))
            ->add($nestedBlock)
            ->add(new SimpleDirective('CustomLog', '"/var/log/apache2/access_log" common'));

        $data['nested multi lines'] = [
            ['main' => [
                new SimpleDirective('ServerRoot', '"/usr"'),
                $block,
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
