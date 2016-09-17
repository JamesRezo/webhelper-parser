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

    /**
     * Gets the regexp that will match the inclusion directives.
     *
     * @return string the regexp that will match the inclusion directives
     */
    public function getInclusionDirective();

    /**
     * Sets the regexp that will match the inclusion directives.
     *
     * @param string $simpleDirective the regexp that will match the inclusion directives
     */
    public function setInclusionDirective($inclusionDirective);

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
}
