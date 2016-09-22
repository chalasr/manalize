<?php

/*
 * This file is part of the Manala package.
 *
 * (c) Manala <contact@manala.io>
 *
 * For the full copyright and license information, please refer to the LICENSE
 * file that was distributed with this source code.
 */

namespace Manala\Tests\Config;

use Manala\Config\Ansible;

class AnsibleTest extends BaseTestConfig
{
    public function testGetPath()
    {
        $ansible = new Ansible($this->getEnvType());

        $this->assertSame('ansible', $ansible->getPath());
    }

    public function testGetOrigin()
    {
        $this->assertOrigin(new Ansible($this->getEnvType()), 'ansible');
    }

    public function testGetTemplate()
    {
        $ansible = new Ansible($this->getEnvType());

        $this->assertSame(realpath($this->getOrigin('ansible').'/group_vars/app.yml'), realpath($ansible->getTemplate()));
    }
}
