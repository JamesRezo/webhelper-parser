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

use WebHelper\Parser\Server\ServerInterface;
use WebHelper\Parser\Directive\DirectiveInterface;
use WebHelper\Parser\Directive\InclusionDirective;

/**
 * Web server configuration generic dumper.
 *
 * @author James <james@rezo.net>
 */
class Dumper
{
    /** @var Server\ServerInterface a server instance */
    private $server;

    /** @var int number of spaces to indent */
    private $indentation = 4;

    /**
     * Setter for the server instance.
     *
     * @see Server\ServerInterface Server Documentation
     *
     * @param Server\ServerInterface $server the server instance
     */
    public function setServer(ServerInterface $server)
    {
        $this->server = $server;

        return $this;
    }

    /**
     * Dumps recursively the active configuration as a file.
     *
     * @param DirectiveInterface $activeConfig the active configuration to dump
     * @param int                $spaces       the indentation spaces
     *
     * @return string the file output
     */
    public function dump(DirectiveInterface $activeConfig, $spaces = 0)
    {
        $config = '';

        foreach ($activeConfig as $directive) {
            if ($directive->isSimple() || $directive instanceof InclusionDirective) {
                $config .= str_repeat(' ', $spaces).
                    sprintf(
                        $this->server->getDumperSimpleDirective(),
                        $directive->getName(),
                        $directive->getValue()
                    ).PHP_EOL;
            } else {
                $config .= str_repeat(' ', $spaces).
                    sprintf(
                        $this->server->getDumperStartDirective(),
                        $directive->getName(),
                        $directive->getValue()
                    ).PHP_EOL;
                $config .= str_repeat(' ', $spaces).$this->dump($directive, $spaces + $this->indentation);
                $config .= str_repeat(' ', $spaces).
                    sprintf(
                        $this->server->getDumperEndDirective(),
                        $directive->getName()
                    ).PHP_EOL;
            }
        }

        return $config;
    }
}
