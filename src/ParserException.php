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

use DomainException;

/**
 * @author James <james@rezo.net>
 */
class ParserException extends DomainException
{
    public static function forEndingKeyNotFound($key)
    {
        return new self(sprintf(
            'No ending directive for %s',
            $key
        ));
    }
}
