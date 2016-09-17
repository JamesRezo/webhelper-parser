<?php

/**
 * This file is part of WebHelper Parser.
 *
 * (c) James <james@rezo.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WebHelper\Parser\Parser;

use InvalidArgumentException;
use Webmozart\Assert\Assert;
use Webmozart\PathUtil\Path;

/**
 * Helper class to check strings.
 *
 * @author James <james@rezo.net>
 */
class Checker
{
    /** @var string a string to check */
    private $string = '';

    /**
     * Sets an empty string if the parameter has the wrong type.
     *
     * @param string $string a string to check
     */
    public function setString($string = '')
    {
        try {
            Assert::string($string);
        } catch (InvalidArgumentException $e) {
            $this->string = '';
        }

        $this->string = $string;

        return $this;
    }

    /**
     * Gets the string.
     *
     * @return string the string
     */
    public function getString()
    {
        return $this->string;
    }

    /**
     * Confirms if a string is an existing absolute path.
     *
     * @return bool true if the string is an existing absolute path, false otherwise
     */
    public function isValidAbsolutePath()
    {
        if (!Path::isAbsolute($this->string)) {
            return false;
        }

        if (!is_dir($this->string)) {
            return false;
        }

        return true;
    }

    /**
     * Confirms if a string is a valid regular expression.
     *
     * @return bool true if the string is a valid regex, false otherwise
     */
    public function isValidRegex()
    {
        if (false === @preg_match($this->string, 'tester')) {
            return false;
        }

        return true;
    }

    /**
     * Confirms if a valid regex string contains a key and a value named suppattern.
     *
     * A simple directive matcher MUST contain a key and a value named subpattern.
     * A starting block directive matcher MUST contain a key and a value named subpattern.
     *
     * @return bool true if the string is valid, false otherwise
     */
    public function hasKeyAndValueSubPattern()
    {
        if (!$this->isValidRegex()) {
            return false;
        }

        if (false === strpos($this->string, '(?<key>') || false === strpos($this->string, '(?<value>')) {
            return false;
        }

        return true;
    }
}
