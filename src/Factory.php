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
use WebHelper\Parser\Parser\Checker;

/**
 * WebHelper Factory.
 *
 * @author James <james@rezo.net>
 */
class Factory
{
    /** @var array [description] */
    private $servers = [];

    /**
     * Factory constructor.
     */
    public function __construct()
    {
        $yaml = new Yaml();
        $config = $yaml->parse(file_get_contents(__DIR__.'/../res/servers.yml'));
        $this->servers = $config['servers'];
    }

    /**
     * Builds a parser instance.
     *
     * @param string $name a server specification name
     *
     * @return ParserInterface a parser instance
     */
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

    /**
     * Builds a server instance.
     *
     * @param string $name a server specification name
     *
     * @return Server\ServerInterface a server instance
     */
    public function createServer($name)
    {
        $server = new Server();
        if (in_array($name, $this->getKnownServers())) {
            $server
                ->setChecker(new Checker())
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

    /**
     * Retrieves known servers from res/servers.yml specifications.
     *
     * @return array known server names
     */
    public function getKnownServers()
    {
        return array_keys($this->servers);
    }
}
