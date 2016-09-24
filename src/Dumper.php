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

use WebHelper\Parser\Directive\DirectiveInterface;
use WebHelper\Parser\Server\ServerInterface;

/**
 * Web server configuration generic dumper.
 *
 * @author James <james@rezo.net>
 */
class Dumper
{
    /** @var Server\ServerInterface a server instance */
    private $server;

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
     *
     * @return string the file output
     */
    public function dump(DirectiveInterface $activeConfig)
    {
        return $activeConfig->dump($this->server);
    }
}
