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
use Manala\Manalize\Env\Config\Make;
use Manala\Manalize\Env\Config\Vagrant;
use Manala\Manalize\Env\Config\Variable\AppName;
use Manala\Manalize\Env\Config\Variable\Tld;
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
        $tld = new Tld('vm');
        $boxVersion = new VagrantBoxVersion('~> 4.0.5');
        $env = EnvFactory::createEnv($envType, $appName, $tld, $this->prophesize(\Iterator::class)->reveal());
        $expectedConfigs = [new Vagrant($envType, $appName, $tld, $boxVersion), new Ansible($envType), new Make($envType)];

        $this->assertInstanceOf(Env::class, $env);
        $this->assertEquals($expectedConfigs, $env->getConfigs());
        $this->assertCount(3, $env->getConfigs());
    }
}
