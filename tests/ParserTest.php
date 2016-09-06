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

class ParserTest extends PHPUnit_Framework_TestCase
{
    private $parser;

    protected function setUp()
    {
        $this->parser = new TestParser();
    }

    /**
     * @expectedException WebHelper\Parser\ParserException
     */
    public function testCantReadConfigFileException()
    {
        $this->parser->setConfigFile(__DIR__.'/tmp/http.conf');
    }

    public function dataReadConfigFileException()
    {
        return [
            'empty1' => [2, __DIR__.'/data/empty1.conf'],
            'empty2' => [2, __DIR__.'/data/empty2.conf'],
        ];
    }

    /**
     * @dataProvider dataReadConfigFileException
     * @expectedException WebHelper\Parser\InvalidConfigException
     */
    public function testReadConfigFileException($expected, $configFile)
    {
        $this->parser->setConfigFile($configFile);
    }

    public function dataReadConfigFile()
    {
        return [
            'file exists 1' => [0, __DIR__.'/data/dummy.conf'],
            'file exists 2' => [0, __DIR__.'/data/dos.conf'],
        ];
    }

    /**
     * @dataProvider dataReadConfigFile
     */
    public function testReadConfigFile($expected, $configFile)
    {
        $this->parser->setConfigFile($configFile);

        $this->assertSame($expected, $this->parser->getLastError());
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
}
