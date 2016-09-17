<?php

/*
 * This file is part of the Manala package.
 *
 * (c) Manala <contact@manala.io>
 *
 * For the full copyright and license information, please refer to the LICENSE
 * file that was distributed with this source code.
 */

namespace Manala\Manalize\Process;

use Manala\Manalize\Config\Config;
use Manala\Manalize\Config\Dumper;
use Manala\Manalize\Config\Vars;
use Manala\Manalize\Env\Env;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Process;

/**
 * Manala build process.
 *
 * @author Robin Chalas <robin.chalas@gmail.com>
 */
class Build extends Process
{
    /**
     * @param string $cwd
     */
    public function __construct($cwd)
    {
        parent::__construct('make setup', $cwd);

        $this->setTimeout(null);
    }

    /**
     * Starts and returns the running process.
     *
     * @return \IteratorAggregate
     */
    public function run($callback = null)
    {
        parent::start();

        return $this;
    }
}
