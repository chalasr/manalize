<?php

/*
 * This file is part of the Manala package.
 *
 * (c) Manala <contact@manala.io>
 *
 * For the full copyright and license information, please refer to the LICENSE
 * file that was distributed with this source code.
 */

namespace Manala\Env\Config;

/**
 * Vagrant configuration.
 *
 * @author Robin Chalas <robin.chalas@gmail.com>
 */
class Vagrant extends Config
{
    /**
     * {@inheritdoc}
     */
    public function getPath()
    {
        return 'Vagrantfile';
    }
}
