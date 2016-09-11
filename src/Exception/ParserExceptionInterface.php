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

/**
 * Error codes for parsing exceptions.
 *
 * @author James <james@rezo.net>
 */
interface ParserExceptionInterface
{
    const UNREADABLE_FILE = 1;
    const EMPTY_CONFIG = 2;
    const BLOCK_DIRECTIVE_ERROR = 3;
    const SIMPLE_DIRECTIVE_ERROR = 4;
    const INVALID_SERVER_PREFIX = 5;
    const INVALID_DIRECTIVE_MATCHER = 6;
}
