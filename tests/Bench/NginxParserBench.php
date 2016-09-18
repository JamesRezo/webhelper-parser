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

class NginxParserBench
{
    public function createNginx()
    {
        $factory = new Factory();
        return $factory->createParser('nginx');
    }

    public function benchParseNginx()
    {
        $nginx = $this->createNginx();
        $nginx->getServer()->setPrefix('/usr/local/Cellar/nginx/1.10.1');
        $activeConfig = $nginx->setConfigFile('/usr/local/etc/nginx/nginx.conf')->getActiveConfig();
    }
}
