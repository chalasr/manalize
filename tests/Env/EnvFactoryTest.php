<?php

/*
 * This file is part of the Manalize project.
 *
 * (c) Manala <contact@manala.io>
 *
 * For the full copyright and license information, please refer to the LICENSE
 * file that was distributed with this source code.
 */

namespace Manala\Manalize\Tests\Env;

use Manala\Manalize\Env\Config\Ansible;
use Manala\Manalize\Env\Config\Gitignore;
use Manala\Manalize\Env\Config\Make;
use Manala\Manalize\Env\Config\Vagrant;
use Manala\Manalize\Env\Config\Variable\AppName;
use Manala\Manalize\Env\Config\Variable\VagrantBoxVersion;
use Manala\Manalize\Env\Env;
use Manala\Manalize\Env\EnvFactory;
use Manala\Manalize\Env\EnvName;
use PHPUnit\Framework\TestCase;

class EnvFactoryTest extends TestCase
{
    public function testCreateEnv()
    {
        $envType = EnvName::SYMFONY();
        $appName = new AppName('rch');
        $boxVersion = new VagrantBoxVersion('~> 3.0.0');
        $env = EnvFactory::createEnv($envType, $appName, $this->prophesize(\Iterator::class)->reveal());
        $expectedConfigs = [new Vagrant($envType, $appName, $boxVersion), new Ansible($envType), new Make($envType), new Gitignore($envType)];

        $this->assertInstanceOf(Env::class, $env);
        $this->assertEquals($expectedConfigs, $env->getConfigs());
        $this->assertCount(4, $env->getConfigs());
    }
}
