<?php


/**
 * This file is part of WebHelper Parser.
 *
 * (c) James <james@rezo.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WebHelper\Parser;

use InvalidArgumentException;

/**
 * Unattended parameters.
 *
 * @author James <james@rezo.net>
 */
class ParserException extends InvalidArgumentException
{
    public static function forFileUnreadable($file)
    {
        return new self(
            sprintf(
                'File "%s" is not readable',
                $file
            ),
            1
        );
    }
}
