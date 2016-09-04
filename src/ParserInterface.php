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

/**
 * Web server configuration generic parser.
 *
 * @author James <james@rezo.net>
 */
interface ParserInterface
{
    /**
     * Setter for the config file to parse.
     *
     * @param string $configFile configuration file
     */
    public function setConfigFile($configFile = '');

    /**
     * Getter for the active config array.
     *
     * @return array active config
     */
    public function getActiveConfig();

    /**
     * Getter for the last error number.
     *
     * @return int error number of the last parsing attempt
     */
    public function getLastError();

    /**
     * Getter for the content of the configuration file.
     *
     * @return string content of the configuration file
     */
    public function getOriginalConfig();
}
