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
use WebHelper\Parser\Directive\SimpleDirective;
use WebHelper\Parser\Directive\BlockDirective;
use WebHelper\Parser\Factory;

class ApacheParserTest extends PHPUnit_Framework_TestCase
{
    private $parser;

    protected function setUp()
    {
        $factory = new Factory();
        $this->parser = $factory->createParser('apache');
        $this->parser->getServer()->setPrefix(realpath(__DIR__.'/data'));
    }

    public function dataApacheParser()
    {
        $data = [];

        $main = new BlockDirective('main');
        $main
            ->add(new SimpleDirective('ServerRoot', '"/usr"'))
            ->add(new SimpleDirective('Listen', '80'))
            ->add(new SimpleDirective('ServerName', 'localhost'))
            ->add(new SimpleDirective('DocumentRoot', '"/var/www/php"'))
        ;
        $data['no multi line'] = [
            $main,
            __DIR__.'/data/apache/no-multi-line.conf',
        ];

        $block = new BlockDirective('Directory', '/');
        $block
            ->add(new SimpleDirective('AllowOverride', 'none'))
            ->add(new SimpleDirective('Require', 'all denied'));
        $main = new BlockDirective('main');
        $main
            ->add(new SimpleDirective('ServerRoot', '"/usr"'))
            ->add($block)
        ;
        $data['one multi line'] = [
            $main,
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
        $main = new BlockDirective('main');
        $main
            ->add(new SimpleDirective('ServerRoot', '"/usr"'))
            ->add($block)
        ;
        $data['nested multi lines'] = [
            $main,
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
     * @expectedException WebHelper\Parser\Exception\InvalidConfigException
     */
    public function testApacheException()
    {
        $this->parser->setConfigFile(__DIR__.'/data/apache/wrong-syntax.conf')->getActiveConfig();
    }

    public function testContinuingLine()
    {
        $directory = new BlockDirective('Directory', '"/home/user/public_html"');
        $directory->add(new SimpleDirective('Require', 'all granted'));
        $expected = new BlockDirective('main');
        $expected->add(new SimpleDirective(
            'LogFormat',
            '"%h %l %u %t \"%r\" %>s %b \"%{Referer}i\" \"%{User-Agent}i\"" combined')
        );
        $expected->add($directory);
        $actual = $this->parser->setConfigFile(__DIR__.'/data/apache/continuing-line.conf')->getActiveConfig();

        $this->assertEquals($expected, $actual);
    }
}
