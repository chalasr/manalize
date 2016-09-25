<?php

/*
 * This file is part of the Manala package.
 *
 * (c) Manala <contact@manala.io>
 *
 * For the full copyright and license information, please refer to the LICENSE
 * file that was distributed with this source code.
 */

namespace Manala\Env;

use Manala\Env\Config\Ansible;
use Manala\Env\Config\Make;
use Manala\Env\Config\Vagrant;

/**
 * Provides Env instances.
 *
 * @author Robin Chalas <robin.chalas@gmail.com>
 */
class EnvFactory
{
    /**
     * @param EnvEnum $type
     *
     * @return Env
     */
    public static function createEnv(EnvEnum $type)
    {
        return new Env(new Ansible($type), new Vagrant($type), new Make($type));
    }
}
