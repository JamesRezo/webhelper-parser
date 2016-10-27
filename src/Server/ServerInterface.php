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
 * a Server instance is .
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
     *
     * @param string $prefix the filesystem path where the web server is installed
     *
     * @throws ServerException if the prefix is invalid
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

    /**
     * Gets the list of binaries that can be run to analyze.
     *
     * @return array the list of binaries that can be run
     */
    public function getBinaries();

    /**
     * Sets the list of binaries that can be run to analyze.
     *
     * @param array $binaries list of controlers
     */
    public function setBinaries(array $binaries);

    /**
     * Gets the parameter string to use to detect version and config file.
     *
     * @return string parameter string
     */
    public function getDetectionParameter();

    /**
     * Sets the parameter string to use to detect version and config file.
     *
     * @param string $parameter parameter string
     */
    public function setDetectionParameter($parameter = '');

    /**
     * Gets the ordered list of methods to apply before the config file turns into an array.
     *
     * @return array the ordered list of methods to apply before convertion
     */
    public function getBeforeMethods();

    /**
     * Sets the ordered list of methods to apply before the config file turns into an array.
     *
     * @param array $methods the ordered list of methods to apply before convertion
     */
    public function setBeforeMethods(array $methods);

    /**
     * Gets the ordered list of methods to apply after the config file has turned into an array.
     *
     * @return array the ordered list of methods to apply after convertion
     */
    public function getAfterMethods();

    /**
     * Sets the ordered list of methods to apply after the config file has turned into an array.
     *
     * @param array $methods the ordered list of methods to apply after convertion
     */
    public function setAfterMethods(array $methods);

    /**
     * Gets the simple directive syntax when dumped.
     *
     * @return string the simple directive syntax when dumped
     */
    public function getDumperSimpleDirective();

    /**
     * Gets the starting block directive syntax when dumped.
     *
     * @return string the starting block directive syntax when dumped
     */
    public function getDumperStartDirective();

    /**
     * Gets the ending block directive syntax when dumped.
     *
     * @return string the ending block directive syntax when dumped
     */
    public function getDumperEndDirective();

    /**
     * Sets the simple directive syntax when dumped.
     *
     * @param string $simpleDirective the simple directive syntax when dumped
     */
    public function setDumperSimpleDirective($simpleDirective);

    /**
     * Sets the starting block directive syntax when dumped.
     *
     * @param string $startMultiLine the starting block directive syntax when dumped
     */
    public function setDumperStartDirective($startMultiLine);

    /**
     * Sets the ending block directive syntax when dumped.
     *
     * @param string $endMultiLine the ending block directive syntax when dumped
     */
    public function setDumperEndDirective($endMultiLine);

    /**
     * Gets the known directives of the server.
     *
     * @return array the known directives of the server
     */
    public function getKnownDirectives();

    /**
     * Sets the known directives of the server.
     *
     * @param array $knownDirectives the known directives of the server
     */
    public function setKnownDirectives(array $knownDirectives);
}
