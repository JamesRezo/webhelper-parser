<?php

/**
 * This file is part of WebHelper Parser.
 *
 * (c) James <james@rezo.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WebHelper\Test\Parser\Bench;

use WebHelper\Parser\Factory;

class ApacheParserBench
{
    public function createApache()
    {
        $factory = new Factory();
        return $factory->createParser('apache');
    }

    public function benchParseApache()
    {
        $apache = $this->createApache();
        $apache->getServer()->setPrefix('/usr');
        $activeConfig = $apache->setConfigFile('/private/etc/apache2/httpd.conf')->getActiveConfig();
    }
}
