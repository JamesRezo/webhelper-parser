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

use WebHelper\Parser\Parser\Checker;

/**
 * A web server instance.
 *
 * @author James <james@rezo.net>
 */
class Server implements ServerInterface
{
    /**
     * a Checker instance.
     *
     * @var WebHelper\Parser\Parser\Checker
     */
    private $checker;

    /**
     * The filesystem path where the web server is installed.
     *
     * It has to be an absolute path.
     *
     * Apache httpd server does not accept a relative prefix path at compilation.
     * Nginx does, but this is a very risky practice...
     * So relative prefix path in nginx configuration will not be considered
     *
     * @var string
     */
    private $prefix = '';

    /**
     * The string to match as a starting multi-line directive.
     *
     * @var string
     */
    private $startMultiLine = '';

    /**
     * The string to match as an ending multi-line directive.
     *
     * @var string
     */
    private $endMultiLine = '';

    /**
     * The string to match a simple directive.
     *
     * @var string
     */
    private $simpleDirective = '';

    /**
     * binaries that can be used to control the webserver.
     *
     * @var array
     */
    private $binaries = [];

    /**
     * the parameter string to use to detect version and config file.
     *
     * @var string
     */
    private $detectionParameter = '';

    /**
     * The ordered list of methods to apply before convertion.
     *
     * @var array
     */
    private $beforeMethods = [];

    /**
     * The ordered list of methods to apply after convertion.
     *
     * @var array
     */
    private $afterMethods = [];

    /**
     * Sets the Checker instance.
     *
     * @param Checker $checker a Checker instance
     */
    public function setChecker(Checker $checker)
    {
        $this->checker = $checker;

        return $this;
    }

    /**
     * Confirms if the server instance has valid parameters.
     *
     * @return bool true if all parameters are initialized, false otherwise
     */
    public function isValid()
    {
        $valid = $this->prefix != '';
        $valid = $valid && $this->startMultiLine != '';
        $valid = $valid && $this->endMultiLine != '';
        $valid = $valid && $this->simpleDirective != '';

        return $valid;
    }

    /**
     * Getter for the prefix.
     *
     * @return string the filesystem path where the web server is installed
     */
    public function getPrefix()
    {
        return $this->prefix;
    }

    /**
     * Sets the prefix of a server instance.
     *
     * @throws ServerException if the prefix is invalid
     *
     * @param string $prefix the filesystem path where the web server is installed
     */
    public function setPrefix($prefix)
    {
        if (!$this->checker->setString($prefix)->getString()) {
            throw ServerException::forInvalidPrefix($prefix, 'The path is expected to be a string. Got: %s');
        }

        if (!$this->checker->isValidAbsolutePath()) {
            throw ServerException::forInvalidPrefix(
                $prefix,
                'The path is expected to be absolute and an existing directory. Got: %s'
            );
        }

        $this->prefix = $prefix;

        return $this;
    }

    /**
     * Gets the regexp that will match the starting block directives.
     *
     * @return string the regexp that will match the starting block directives
     */
    public function getStartMultiLine()
    {
        return $this->startMultiLine;
    }

    /**
     * Sets the regexp that will match the starting block directives.
     *
     * @param string $startMultiLine the regexp that will match the starting block directives
     */
    public function setStartMultiLine($startMultiLine)
    {
        if (!$this->checker->setString($startMultiLine)->getString()) {
            throw ServerException::forInvalidMatcher(
                $startMultiLine,
                'The starting block directive matcher is expected to be a string. Got: %s'
            );
        }

        if (!$this->checker->hasKeyAndValueSubPattern()) {
            throw ServerException::forInvalidMatcher(
                $startMultiLine,
                'The starting block directive matcher is expected to be a regexp '.
                'containing named subpatterns "key" and "value". Got: %s'
            );
        }

        $this->startMultiLine = $startMultiLine;

        return $this;
    }

    /**
     * Gets the regexp that will match the ending block directives.
     *
     * @return string the regexp that will match the ending block directives
     */
    public function getEndMultiLine()
    {
        return $this->endMultiLine;
    }

    /**
     * Sets the regexp that will match the ending block directives.
     *
     * @param string $endMultiLine the regexp that will match the ending block directives
     */
    public function setEndMultiLine($endMultiLine)
    {
        if (!$this->checker->setString($endMultiLine)->getString()) {
            throw ServerException::forInvalidMatcher(
                $endMultiLine,
                'The ending block directive matcher is expected to be a string. Got: %s'
            );
        }

        if (!$this->checker->isValidRegex()) {
            throw ServerException::forInvalidMatcher(
                $endMultiLine,
                'The ending block directive matcher is expected to be a regexp.'
            );
        }

        $this->endMultiLine = $endMultiLine;

        return $this;
    }

    /**
     * Gets the regexp that will match the simple directives.
     *
     * @return string the regexp that will match the simple directives
     */
    public function getSimpleDirective()
    {
        return $this->simpleDirective;
    }

    /**
     * Sets the regexp that will match the simple directives.
     *
     * @param string $simpleDirective the regexp that will match the simple directives
     */
    public function setSimpleDirective($simpleDirective)
    {
        if (!$this->checker->setString($simpleDirective)->getString()) {
            throw ServerException::forInvalidMatcher(
                $simpleDirective,
                'The simple directive matcher is expected to be a string. Got: %s'
            );
        }

        if (!$this->checker->hasKeyAndValueSubPattern()) {
            throw ServerException::forInvalidMatcher(
                $simpleDirective,
                'The simple directive matcher is expected to be a regexp '.
                'containing named subpatterns "key" and "value". Got: %s'
            );
        }

        $this->simpleDirective = $simpleDirective;

        return $this;
    }

    /**
     * Gets the list of binaries that can be run to analyze.
     *
     * @return array the list of binaries that can be run
     */
    public function getBinaries()
    {
        return $this->binaries;
    }

    /**
     * Sets the list of binaries that can be run to analyze.
     *
     * @param array $binaries list of controlers
     */
    public function setBinaries(array $binaries)
    {
        $this->binaries = $binaries;

        return $this;
    }

    /**
     * Sets the parameter string to use to detect version and config file.
     *
     * @param string $parameter parameter string
     */
    public function setDetectionParameter($parameter = '')
    {
        $this->detectionParameter = $parameter;

        return $this;
    }

    /**
     * Gets the ordered list of methods to apply before the config file turns into an array.
     *
     * @return array the ordered list of methods to apply before convertion
     */
    public function getBeforeMethods()
    {
        return $this->beforeMethods;
    }

    /**
     * Sets the ordered list of methods to apply before the config file turns into an array.
     *
     * @param array $methods the ordered list of methods to apply before convertion
     */
    public function setBeforeMethods(array $methods)
    {
        $this->beforeMethods = $methods;

        return $this;
    }

    /**
     * Gets the ordered list of methods to apply after the config file has turned into an array.
     *
     * @return array the ordered list of methods to apply after convertion
     */
    public function getAfterMethods()
    {
        return $this->afterMethods;
    }

    /**
     * Sets the ordered list of methods to apply after the config file has turned into an array.
     *
     * @param array $methods the ordered list of methods to apply after convertion
     */
    public function setAfterMethods(array $methods)
    {
        $this->afterMethods = $methods;

        return $this;
    }
}
