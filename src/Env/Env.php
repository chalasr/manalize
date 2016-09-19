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

use Manala\Config\Config;

/**
 * Manala Env.
 *
 * @author Robin Chalas <robin.chalas@gmail.com>
 */
class Env
{
    private $configs = [];

    /**
     * @param Config $configs
     */
    public function __construct(Config ...$configs)
    {
        $this->configs = $configs;
    }

    /**
     * @return Config[]
     */
    public function getConfigs()
    {
        return $this->configs;
    }
}
