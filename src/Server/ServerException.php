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

use WebHelper\Parser\Exception\ParserExceptionInterface;
use InvalidArgumentException;

/**
 * Unattended parameters.
 *
 * @author James <james@rezo.net>
 */
class ServerException extends InvalidArgumentException implements ParserExceptionInterface
{
    /**
     * The exception to throw if the prefix is invalid.
     *
     * @param string $prefix  the prefix
     * @param string $message the error message
     *
     * @return ServerException the exception to throw
     */
    public static function forInvalidPrefix($prefix, $message)
    {
        return new self(
            sprintf(
                $message,
                $prefix
            ),
            self::INVALID_SERVER_PREFIX
        );
    }

    /**
     * The exception to throw if a directive matcher is invalid.
     *
     * @param string $matcher the matcher
     * @param string $message the error message
     *
     * @return ServerException the exception to throw
     */
    public static function forInvalidMatcher($matcher, $message)
    {
        return new self(
            sprintf(
                $message,
                $matcher
            ),
            self::INVALID_DIRECTIVE_MATCHER
        );
    }
}
