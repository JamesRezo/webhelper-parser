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
use WebHelper\Parser\Server\Server;

class ParserTest extends PHPUnit_Framework_TestCase
{
    private $parser;

    protected function setUp()
    {
        $this->parser = new TestParser();
    }

    /**
     * @expectedException WebHelper\Parser\Exception\ParserException
     */
    public function testCantReadConfigFileException()
    {
        $this->parser->setConfigFile(__DIR__.'/tmp/http.conf');
    }

    public function dataReadConfigFileException()
    {
        return [
            'empty1' => [__DIR__.'/data/empty1.conf'],
            'empty2' => [__DIR__.'/data/empty2.conf'],
        ];
    }

    /**
     * @dataProvider dataReadConfigFileException
     * @expectedException WebHelper\Parser\Exception\InvalidConfigException
     */
    public function testReadConfigFileException($configFile)
    {
        $this->parser->setConfigFile($configFile);
    }

    public function dataIntegration()
    {
        return [
            'unix' => [__DIR__.'/data/dummy.conf'],
            'windows' => [__DIR__.'/data/dos.conf'],
        ];
    }

    /**
     * @dataProvider dataIntegration
     */
    public function testIntegration($configFile)
    {
        $this->parser->setConfigFile($configFile);
        $activeConfig = $this->parser->getActiveConfig();

        //No comment lines
        $this->assertFalse(in_array('#dummy file', $activeConfig));
        $this->assertFalse(in_array('Directive dummy_value; #comment', $activeConfig));
        $this->assertTrue(in_array('Directive dummy_value;', $activeConfig));

        //No blank lines
        $this->assertFalse(in_array('', $activeConfig), 'at least one empty line');
    }

    public function testGetServer()
    {
        $this->parser->setServer(new Server());

        $this->assertEquals('', $this->parser->getServer()->getPrefix());
    }
}
