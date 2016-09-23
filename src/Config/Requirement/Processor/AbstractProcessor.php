<?php

/*
 * This file is part of the Manala package.
 *
 * (c) Manala <contact@manala.io>
 *
 * For the full copyright and license information, please refer to the LICENSE
 * file that was distributed with this source code.
 */

namespace Manala\Config\Requirement\Processor;

use Manala\Config\Requirement\Exception\MissingRequirementException;
use Symfony\Component\Process\Process;

abstract class AbstractProcessor
{
    /**
     * Process a required or suggested executable identified by its name (vagrant, ansible, etc.), i.e. run the proper
     * console command in order to test if it is available on the current host. Each concrete processor is responsible
     * for running the proper console command (eg `<binary> --version` for an executable, `vagrant plugin list for a`
     * vagrant plugin, etc.)
     *
     * @param string $name The name of the binary (or vagrant plugin or ...) to check
     *
     * @throws MissingRequirementException When processed command is unsuccessful
     *
     * @return string|null Returns the standard output if tool is available, false otherwise.
     */
    public function process($name)
    {
        $command = $this->getCommand($name);
        $process = new Process($command);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new MissingRequirementException();
        }

        return $process->getOutput();
    }

    /**
     * Proper command to run in order to check if an executable is available.
     *
     * @param string $name
     *
     * @return string
     */
    abstract public function getCommand($name);
}
