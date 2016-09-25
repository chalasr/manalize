<?php

/*
 * This file is part of the Manala package.
 *
 * (c) Manala <contact@manala.io>
 *
 * For the full copyright and license information, please refer to the LICENSE
 * file that was distributed with this source code.
 */

namespace Manala\Tests\Env\Config;

use Manala\Env\Config\Config;
use Manala\Env\EnvEnum;

class BaseTestConfig extends \PHPUnit_Framework_TestCase
{
    const ENV = EnvEnum::SYMFONY;

    protected function assertOrigin(Config $config, $name)
    {
        $this->assertSame(realpath($this->getOrigin($name)), realpath($config->getOrigin()));
    }

    protected function getEnvType()
    {
        return EnvEnum::create(self::ENV);
    }

    protected function getOrigin($name)
    {
        return MANALA_DIR.'/src/Resources/'.self::ENV.'/'.$name;
    }
}
