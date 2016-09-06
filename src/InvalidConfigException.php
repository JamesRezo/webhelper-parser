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
 * Invalid configuration file content.
 *
 * @author James <james@rezo.net>
 */
class InvalidConfigException extends DomainException
{
    public static function forEmptyConfig($file)
    {
        return new self(sprintf(
            'File "%s" returns an empty configuration',
            $file
        ), 2);
    }

    public static function forEndingKeyNotFound($key)
    {
        return new self(sprintf(
            'No ending directive for %s',
            $key
        ), 3);
    }
}
