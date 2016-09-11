<?php


/**
 * This file is part of WebHelper Parser.
 *
 * (c) James <james@rezo.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WebHelper\Parser\Exception;

use DomainException;

/**
 * Invalid configuration file content.
 *
 * @author James <james@rezo.net>
 */
class InvalidConfigException extends DomainException implements ParserExceptionInterface
{
    /**
     * the exception to throw if the configuration file results as an empty active config.
     *
     * @param string $file file pathname
     *
     * @return InvalidConfigException the exception to throw
     */
    public static function forEmptyConfig($file)
    {
        return new self(sprintf(
            'File "%s" returns an empty configuration',
            $file
        ), self::EMPTY_CONFIG);
    }

    /**
     * the exception to throw if a directive has no ending key.
     *
     * @param string $key a directive name
     *
     * @return InvalidConfigException the exception to throw
     */
    public static function forEndingKeyNotFound($key)
    {
        return new self(sprintf(
            'No ending directive for %s',
            $key
        ), self::BLOCK_DIRECTIVE_ERROR);
    }

    /**
     * the exception to throw if a simple directive does not match against the accepted syntax.
     *
     * @param string $line the line of the simple directive
     *
     * @return InvalidConfigException the exception to throw
     */
    public static function forSimpleDirectiveSyntaxError($line)
    {
        return new self(sprintf(
            'Syntax error for the line "%s"',
            $line
        ), self::SIMPLE_DIRECTIVE_ERROR);
    }
}
