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

use InvalidArgumentException;
use Webmozart\Assert\Assert;
use Webmozart\PathUtil\Path;

/**
 * A web server instance.
 *
 * @author James <james@rezo.net>
 */
class Server implements ServerInterface
{
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
     * Sets the prefix of a server isntance.
     *
     * @throws ServerException if the prefix is invalid
     *
     * @param string $prefix the filesystem path where the web server is installed
     */
    public function setPrefix($prefix)
    {
        try {
            Assert::string($prefix);
        } catch (InvalidArgumentException $e) {
            throw ServerException::forInvalidPrefix($prefix, 'The path is expected to be a string. Got: %s');
        }

        if (!Path::isAbsolute($prefix)) {
            throw ServerException::forInvalidPrefix($prefix, 'The path is expected to be absolute. Got: %s');
        }

        if (!is_dir($prefix)) {
            throw ServerException::forInvalidPrefix(
                $prefix,
                'The path is expected to be an existing directory. Got: %s'
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
        $this->startMultiLine = $this->checkString(
            $startMultiLine,
            'The starting block directive matcher is expected to be a string. Got: %s',
            'The starting block directive matcher is expected to be a regexp '.
            'containing named subpatterns "key" and "value". Got: %s'
        );

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
        $this->endMultiLine = $this->checkString(
            $endMultiLine,
            'The endind block directive matcher is expected to be a string. Got: %s',
            'The ending block directive matcher is expected to be a regexp . Got: %s',
            false
        );

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
        $this->simpleDirective = $this->checkString(
            $simpleDirective,
            'The simple directive matcher is expected to be a string. Got: %s',
            'The simple directive matcher is expected to be a regexp '.
            'containing named subpatterns "key" and "value". Got: %s'
        );

        return $this;
    }

    /**
     * Checks if the string matches some criterias.
     *
     * @param string $string     the string to check
     * @param string $message1   message if not a string
     * @param string $message2   message if not a regexp
     * @param bool   $subpattern confirms the presence of subpatterns "key" and "value"
     *
     * @throws ServerException if the string is invalid
     *
     * @return string the string
     */
    private function checkString($string, $message1, $message2, $subpattern = true)
    {
        try {
            Assert::string($string);
        } catch (InvalidArgumentException $e) {
            throw ServerException::forInvalidMatcher($string, $message1);
        }

        if (!$this->isValidRegex($string, $subpattern)) {
            throw ServerException::forInvalidMatcher($string, $message2);
        }

        return $string;
    }

    /**
     * Confirms if a matcher is a valid reguler expression.
     *
     * A directive matcher MUST contain a key and a value named subpattern.
     *
     * @param string $matcher    the matcher to validate
     * @param bool   $subpattern confirms the presence of subpatterns "key" and "value"
     *
     * @return bool true if the matcher is valid, false otherwise
     */
    private function isValidRegex($matcher, $subpattern = true)
    {
        if (false === @preg_match($matcher, 'tester')) {
            return false;
        }

        if ($subpattern && (false === strpos($matcher, '(?<key>') || false === strpos($matcher, '(?<value>'))) {
            return false;
        }

        return true;
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
