<?php

/**
 * This file is part of WebHelper Parser.
 *
 * (c) James <james@rezo.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WebHelper\Parser\Server;

/**
 * Describes a web server instance.
 *
 * a Server instance is <blah...> .
 *
 * @author James <james@rezo.net>
 */
interface ServerInterface
{
    /**
     * Confirms if the server instance has valid parameters.
     *
     * @return bool true if all parameters are initialized, false otherwise
     */
    public function isValid();

    /**
     * Getter for the prefix.
     *
     * @return string the filesystem path where the web server is installed
     */
    public function getPrefix();

    /**
     * Sets the prefix of a server isntance.
     *
     * @throws ServerException if the prefix is invalid
     *
     * @param string $prefix the filesystem path where the web server is installed
     */
    public function setPrefix($prefix);

    /**
     * Gets the regexp that will match the starting block directives.
     *
     * @return string the regexp that will match the starting block directives
     */
    public function getStartMultiLine();

    /**
     * Sets the regexp that will match the starting block directives.
     *
     * @param string $startMultiLine the regexp that will match the starting block directives
     */
    public function setStartMultiLine($startMultiLine);

    /**
     * Gets the regexp that will match the ending block directives.
     *
     * @return string the regexp that will match the ending block directives
     */
    public function getEndMultiLine();

    /**
     * Sets the regexp that will match the ending block directives.
     *
     * @param string $endMultiLine the regexp that will match the ending block directives
     */
    public function setEndMultiLine($endMultiLine);

    /**
     * Gets the regexp that will match the simple directives.
     *
     * @return string the regexp that will match the simple directives
     */
    public function getSimpleDirective();

    /**
     * Sets the regexp that will match the simple directives.
     *
     * @param string $simpleDirective the regexp that will match the simple directives
     */
    public function setSimpleDirective($simpleDirective);
}
