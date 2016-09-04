<?php


/**
 * This file is part of WebHelper Parser.
 *
 * (c) James <james@rezo.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WebHelper\Test\Parser;

use WebHelper\Parser\Parser as BaseParser;

class TestParser extends BaseParser
{
    public function getActiveConfig()
    {
        return $this->activeConfig;
    }
}
