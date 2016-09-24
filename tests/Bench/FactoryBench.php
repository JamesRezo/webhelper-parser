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

class FactoryBench
{
    public function benchCreateApache()
    {
        $factory = new Factory();
        $factory->createParser('apache');
    }

    public function benchCreateNginx()
    {
        $factory = new Factory();
        $factory->createParser('nginx');
    }
}
