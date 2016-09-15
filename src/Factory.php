<?php

/**
 * This file is part of WebHelper Parser.
 *
 * (c) James <james@rezo.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WebHelper\Parser;

use Symfony\Component\Yaml\Yaml;
use WebHelper\Parser\Server\Server;

/**
 * WebHelper Factory.
 *
 * @author James <james@rezo.net>
 */
class Factory
{
    private $servers = [];

    public function __construct()
    {
        $yaml = new Yaml();
        $config = $yaml->parse(file_get_contents(__DIR__.'/../res/servers.yml'));
        $this->servers = $config['servers'];
    }

    public function createParser($name)
    {
        $parser = new Parser();

        $parser->setServer($this->createServer($name));

        $compiler = new Compiler(
            $parser->getServer()->getStartMultiLine(),
            $parser->getServer()->getEndMultiLine(),
            $parser->getServer()->getSimpleDirective()
        );

        $parser->setCompiler($compiler);

        return $parser;
    }

    public function createServer($name)
    {
        $server = new Server();
        if (in_array($name, $this->getKnownServers())) {
            $server
                ->setStartMultiLine($this->servers[$name]['directives']['start_multiline'])
                ->setEndMultiLine($this->servers[$name]['directives']['end_multiline'])
                ->setSimpleDirective($this->servers[$name]['directives']['simple'])
                ->setBinaries($this->servers[$name]['controlers'])
                ->setDetectionParameter($this->servers[$name]['switch']['detect'])
                ->setBeforeMethods($this->servers[$name]['parser']['before'])
                ->setAfterMethods($this->servers[$name]['parser']['after'])
            ;
        }

        return $server;
    }

    public function getKnownServers()
    {
        return array_keys($this->servers);
    }
}
