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

use InvalidArgumentException;

/**
 * Unattended parameters.
 *
 * @author James <james@rezo.net>
 */
class ParserException extends InvalidArgumentException implements ParserExceptionInterface
{
    /**
     * the exception to throw if the configuration file is unreadable.
     *
     * @param string $file file pathname
     *
     * @return ParserException the exception to throw
     */
    public static function forFileUnreadable($file)
    {
        return new self(
            sprintf(
                'File "%s" is not readable',
                $file
            ),
            self::UNREADABLE_FILE
        );
    }
}
