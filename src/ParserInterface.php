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

/**
 * Web server configuration generic parser.
 *
 * @author James <james@rezo.net>
 */
interface ParserInterface
{
    /**
     * Setter for the server instance.
     *
     * @see Server\ServerInterface Server Documentation
     *
     * @param Server\ServerInterface $server the server instance
     */
    public function setServer(ServerInterface $server);

    /**
     * Setter for the config file to parse.
     *
     * @param string $configFile configuration file
     */
    public function setConfigFile($configFile = '');

    /**
     * Getter for the server instance.
     *
     * @see Server\ServerInterface Server Documentation
     *
     * @param Server\ServerInterface the server instance
     */
    public function getServer();

    /**
     * Getter for the active config main context.
     *
     * @return Directive\DirectiveInterface the active config
     */
    public function getActiveConfig();

    /**
     * Getter for the content of the configuration file.
     *
     * @return string content of the configuration file
     */
    public function getOriginalConfig();
}
