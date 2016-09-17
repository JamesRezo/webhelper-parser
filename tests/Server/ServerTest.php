<?php

/**
 * This file is part of WebHelper Parser.
 *
 * (c) James <james@rezo.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WebHelper\Test\Parser\Server;

use PHPUnit_Framework_TestCase;
use WebHelper\Parser\Server\Server;
use WebHelper\Parser\Parser\Checker;

class ServerTest extends PHPUnit_Framework_TestCase
{
    private $server;

    protected function setUp()
    {
        $this->server = new Server();
        $this->server->setChecker(new Checker());
    }

    public function dataSetWrongPrefix()
    {
        return [
            'not a string' => [null],
            'not an absolute path' => ['null'],
            'not an existing absolute path' => [__DIR__.'/null'],
        ];
    }

    /**
     * @dataProvider dataSetWrongPrefix
     * @expectedException WebHelper\Parser\Server\ServerException
     */
    public function testSetWrongPrefix($prefix)
    {
        $this->server->setPrefix($prefix);
    }

    public function testSetValidPrefix()
    {
        $prefix = realpath(__DIR__.'/../data');
        $this->server->setPrefix($prefix);

        $this->assertEquals($prefix, $this->server->getPrefix());
    }

    public function dataSetWrongSimpleDirective()
    {
        return [
            'not a string' => [null],
            'not a regexp' => ['null'],
            'missing both named subpatterns' => ['/test(?<context>.+)/'],
            'missing key named subpattern' => ['/test(?<value>.+)/'],
            'missing value named subpattern' => ['/test(?<key>.+)/'],
        ];
    }

    /**
     * @dataProvider dataSetWrongSimpleDirective
     * @expectedException WebHelper\Parser\Server\ServerException
     */
    public function testSetWrongSimpleDirective($simpleDirective)
    {
        $this->server->setSimpleDirective($simpleDirective);
    }

    public function testSetValidSimpleDirective()
    {
        $simpleDirective = '/^(?<key>\w+)(?<value>.+)$/';
        $this->server->setSimpleDirective($simpleDirective);

        $this->assertEquals($simpleDirective, $this->server->getSimpleDirective());
    }

    public function dataSetWrongStartDirective()
    {
        return [
            'not a string' => [null],
            'not a regexp' => ['null'],
            'missing both named subpatterns' => ['/test(?<context>.+)/'],
            'missing key named subpattern' => ['/test(?<value>.+)/'],
            'missing value named subpattern' => ['/test(?<key>.+)/'],
        ];
    }

    /**
     * @dataProvider dataSetWrongStartDirective
     * @expectedException WebHelper\Parser\Server\ServerException
     */
    public function testSetWrongStartDirective($startDirective)
    {
        $this->server->setStartMultiLine($startDirective);
    }

    public function testSetValidStartDirective()
    {
        $startDirective = '/^<(?<key>\w+)(?<value>.+)>$/';
        $this->server->setStartMultiLine($startDirective);

        $this->assertEquals($startDirective, $this->server->getStartMultiLine());
    }

    public function dataSetWrongEndorInclusionDirective()
    {
        return [
            'not a string' => [null],
            'not a regexp' => ['null'],
        ];
    }

    /**
     * @dataProvider dataSetWrongEndorInclusionDirective
     * @expectedException WebHelper\Parser\Server\ServerException
     */
    public function testSetWrongEndDirective($endDirective)
    {
        $this->server->setEndMultiLine($endDirective);
    }

    public function testSetValidEndDirective()
    {
        $endDirective = '/^<\/end>/';
        $this->server->setEndMultiLine($endDirective);

        $this->assertEquals($endDirective, $this->server->getEndMultiLine());
    }

    public function dataIsValid()
    {
        $server = new Server();
        $server
            ->setChecker(new Checker())
            ->setPrefix(realpath(__DIR__.'/../data'))
            ->setEndMultiLine('/^<\/end>/')
            ->setStartMultiLine('/^<(?<key>\w+)(?<value>.+)>$/')
            ->setSimpleDirective('/^(?<key>\w+)(?<value>.+)$/')
        ;

        return [
            'not configured' => [false, new Server()],
            'fully configured' => [true, $server],
        ];
    }

    /**
     * @dataProvider dataIsValid
     */
    public function testIsValid($expected, $server)
    {
        $this->assertEquals($expected, $server->isValid());
    }

    public function testBinaries()
    {
        $this->server->setBinaries(['test']);

        $this->assertEquals(['test'], $this->server->getBinaries());
    }

    public function testDetectionParameter()
    {
        $this->server->setDetectionParameter(' -test');

        $this->assertEquals(' -test', $this->server->getDetectionParameter());
    }

    /**
     * @dataProvider dataSetWrongEndorInclusionDirective
     * @expectedException WebHelper\Parser\Server\ServerException
     */
    public function testSetWrongInclusionDirective($inclusionDirective)
    {
        $this->server->setInclusionDirective($inclusionDirective);
    }
}
