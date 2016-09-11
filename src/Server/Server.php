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
     * @param string $simpleDirective the regexp that will match the starting block directives
     */
    public function setStartMultiLine($startMultiLine)
    {
        try {
            Assert::string($startMultiLine);
        } catch (InvalidArgumentException $e) {
            throw ServerException::forInvalidMatcher(
                $startMultiLine,
                'The starting block directive matcher is expected to be a string. Got: %s'
            );
        }

        if (!$this->isValidRegex($startMultiLine)) {
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
        try {
            Assert::string($endMultiLine);
        } catch (InvalidArgumentException $e) {
            throw ServerException::forInvalidMatcher(
                $endMultiLine,
                'The endind block directive matcher is expected to be a string. Got: %s'
            );
        }

        if (!$this->isValidRegex($endMultiLine, false)) {
            throw ServerException::forInvalidMatcher(
                $endMultiLine,
                'The ending block directive matcher is expected to be a regexp . Got: %s'
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
        try {
            Assert::string($simpleDirective);
        } catch (InvalidArgumentException $e) {
            throw ServerException::forInvalidMatcher(
                $simpleDirective,
                'The simple directive matcher is expected to be a string. Got: %s'
            );
        }

        if (!$this->isValidRegex($simpleDirective)) {
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
}
